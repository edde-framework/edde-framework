<?php
	declare(strict_types = 1);

	namespace Edde\Common\Template;

	use Edde\Api\Container\LazyContainerTrait;
	use Edde\Api\Crypt\LazyCryptEngineTrait;
	use Edde\Api\File\IFile;
	use Edde\Api\File\IRootDirectory;
	use Edde\Api\Node\INode;
	use Edde\Api\Resource\LazyResourceManagerTrait;
	use Edde\Api\Template\CompilerException;
	use Edde\Api\Template\ICompiler;
	use Edde\Api\Template\IHelperSet;
	use Edde\Api\Template\IMacro;
	use Edde\Api\Template\IMacroSet;
	use Edde\Api\Template\MacroException;
	use Edde\Common\Deffered\AbstractDeffered;
	use Edde\Common\Reflection\ReflectionUtils;

	/**
	 * Default implementation of template compiler.
	 */
	class Compiler extends AbstractDeffered implements ICompiler {
		use LazyContainerTrait;
		use LazyResourceManagerTrait;
		use LazyCryptEngineTrait;
		/**
		 * @var IRootDirectory
		 */
		protected $rootDirectory;
		/**
		 * @var IFile
		 */
		protected $source;
		/**
		 * @var IMacro[]
		 */
		protected $macroList = [];
		/**
		 * @var IHelperSet[]
		 */
		protected $helperSetList = [];
		/**
		 * stack of compiled files (when compiler is reused)
		 *
		 * @var \SplStack
		 */
		protected $stack;
		/**
		 * variable context between macros (any macro should NOT hold a context)
		 *
		 * @var array
		 */
		protected $context = [];

		/**
		 * @param IFile $source
		 */
		public function __construct(IFile $source) {
			$this->source = $source;
		}

		/**
		 * @param IRootDirectory $rootDirectory
		 */
		public function lazyRootDirectory(IRootDirectory $rootDirectory) {
			$this->rootDirectory = $rootDirectory;
		}

		/**
		 * @inheritdoc
		 */
		public function registerMacroSet(IMacroSet $macroSet): ICompiler {
			foreach ($macroSet->getMacroList() as $macro) {
				$this->registerMacro($macro);
			}
			return $this;
		}

		/**
		 * @inheritdoc
		 */
		public function registerMacro(IMacro $macro): ICompiler {
			$this->macroList[$name = $macro->getName()] = $macro;
			if (isset($this->macroList[$compile = ('m:' . $name)]) === false) {
				$this->macroList[$compile] = $macro;
			}
			if (isset($this->macroList[$compile = ('t:' . $name)]) === false) {
				$this->macroList[$compile] = $macro;
			}
			return $this;
		}

		/**
		 * @inheritdoc
		 * @throws \Exception
		 */
		public function template(array $importList = []) {
			$this->use();
			$this->context = [];
			try {
				$nameList = [
					$this->source->getPath(),
				];
				foreach ($importList as $import) {
					$nameList[] = $import->getPath();
				}
				$this->setVariable('name', sha1(implode(',', $nameList)));
				$this->setVariable('name-list', $nameList);
				foreach ($importList as $import) {
					$this->file($import)
						->setMeta('included', true);
				}
				return $this->macro($this->file($this->source));
			} catch (\Exception $exception) {
				$root = $this->rootDirectory->getDirectory();
				/**
				 * Ugly hack to set exception message without messing with a trace.
				 */
				$stackList = [
					$this->source->getPath() => $this->source->getRelativePath($root),
				];
				/** @var $file IFile */
				while ($this->stack->isEmpty() === false) {
					$file = $this->stack->pop();
					$stackList[$file->getPath()] = $file->getRelativePath($root);
				}
				ReflectionUtils::setProperty($exception, 'message', sprintf("Template compilation failed: %s\nTemplate file stack:\n%s", $exception->getMessage(), implode(",\n", array_reverse($stackList, true))));
				throw $exception;
			}
		}

		/**
		 * @inheritdoc
		 */
		public function setVariable(string $name, $value): ICompiler {
			$this->context[$name] = $value;
			return $this;
		}

		/**
		 * @inheritdoc
		 * @throws CompilerException
		 */
		public function file(IFile $source): INode {
			$this->use();
			$this->stack->push($source);
			$this->compile($root = $this->resourceManager->resource($source));
			$this->stack->pop();
			return $root;
		}

		/**
		 * @inheritdoc
		 * @throws CompilerException
		 */
		public function compile(INode $macro) {
			return $this->run($macro, __FUNCTION__, 't');
		}

		/**
		 * @param INode $macro
		 * @param string $method
		 * @param string $inline
		 *
		 * @return mixed
		 * @throws CompilerException
		 */
		protected function run(INode $macro, string $method, string $inline) {
			if (isset($this->macroList[$name = $macro->getName()]) === false) {
				throw new CompilerException(sprintf('Unknown macro [%s].', $macro->getPath()));
			}
			foreach ($macro->getAttributeList() as $k => $v) {
				if (isset($this->macroList[$k]) && strpos($k, $inline . ':') === 0) {
					$this->macroList[$k]->{$method . 'Inline'}($macro, $this);
				}
			}
			return $this->macroList[$name]->{$method}($macro, $this);
		}

		/**
		 * @inheritdoc
		 * @throws CompilerException
		 */
		public function macro(INode $macro) {
			return $this->run($macro, __FUNCTION__, 'm');
		}

		/**
		 * @inheritdoc
		 */
		public function getSource(): IFile {
			return $this->source;
		}

		/**
		 * @inheritdoc
		 */
		public function getCurrent(): IFile {
			return $this->stack->top();
		}

		/**
		 * @inheritdoc
		 */
		public function isLayout(): bool {
			return $this->stack->count() === 1;
		}

		/**
		 * @inheritdoc
		 */
		public function helper(INode $macro, $value) {
			$this->use();
			$result = null;
			foreach ($this->helperSetList as $helperSet) {
				foreach ($helperSet->getHelperList() as $helper) {
					if (($result = $helper->helper($macro, $this, $value)) !== null) {
						break 2;
					}
				}
			}
			return $result;
		}

		/**
		 * @inheritdoc
		 * @throws MacroException
		 */
		public function block(string $name, array $nodeList): ICompiler {
			$blockList = $this->getBlockList();
			$blockList[$name] = $nodeList;
			$this->setVariable(static::class . '/block-list', $blockList);
			return $this;
		}

		/**
		 * @inheritdoc
		 */
		public function getBlockList(): array {
			return $this->getVariable(static::class . '/block-list', []);
		}

		/**
		 * @inheritdoc
		 */
		public function getVariable(string $name, $default = null) {
			if (isset($this->context[$name]) === false) {
				$this->context[$name] = $default;
			}
			return $this->context[$name];
		}

		/**
		 * @inheritdoc
		 * @throws MacroException
		 */
		public function getBlock(string $name): array {
			$blockList = $this->getVariable(self::class . '/block-list', []);
			if (isset($blockList[$name]) === false) {
				throw new MacroException(sprintf('Requested unknown block [%s].', $name));
			}
			return $blockList[$name];
		}

		/**
		 * @inheritdoc
		 */
		protected function prepare() {
			$this->stack = new \SplStack();
			foreach ($this->macroList as $macro) {
				if ($macro->hasHelperSet()) {
					$this->registerHelperSet($macro->getHelperSet());
				}
			}
		}

		/**
		 * @inheritdoc
		 */
		public function registerHelperSet(IHelperSet $helperSet): ICompiler {
			$this->helperSetList[] = $helperSet;
			return $this;
		}
	}

<?php
	declare(strict_types = 1);

	namespace Edde\Common\Template;

	use Edde\Api\Container\IContainer;
	use Edde\Api\Crypt\ICryptEngine;
	use Edde\Api\File\IFile;
	use Edde\Api\File\IRootDirectory;
	use Edde\Api\Node\INode;
	use Edde\Api\Resource\IResourceManager;
	use Edde\Api\Template\CompilerException;
	use Edde\Api\Template\ICompiler;
	use Edde\Api\Template\IHelperSet;
	use Edde\Api\Template\IInline;
	use Edde\Api\Template\IMacro;
	use Edde\Api\Template\IMacroSet;
	use Edde\Api\Template\MacroException;
	use Edde\Common\Node\NodeIterator;
	use Edde\Common\Reflection\ReflectionUtils;
	use Edde\Common\Usable\AbstractUsable;

	/**
	 * Default implementation of template compiler.
	 */
	class Compiler extends AbstractUsable implements ICompiler {
		/**
		 * @var IContainer
		 */
		protected $container;
		/**
		 * @var IResourceManager
		 */
		protected $resourceManager;
		/**
		 * @var ICryptEngine
		 */
		protected $cryptEngine;
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
		 * @var IInline[]
		 */
		protected $inlineList = [];
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
		 * @param IContainer $container
		 */
		public function lazyContainer(IContainer $container) {
			$this->container = $container;
		}

		/**
		 * @param IResourceManager $resourceManager
		 */
		public function lazyResourceManager(IResourceManager $resourceManager) {
			$this->resourceManager = $resourceManager;
		}

		/**
		 * @param ICryptEngine $cryptEngine
		 */
		public function lazyCryptEngine(ICryptEngine $cryptEngine) {
			$this->cryptEngine = $cryptEngine;
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
			foreach ($macroSet->getInlineList() as $inline) {
				$this->registerInline($inline);
			}
			return $this;
		}

		/**
		 * @inheritdoc
		 */
		public function registerMacro(IMacro $macro): ICompiler {
			$this->macroList[$macro->getName()] = $macro;
			return $this;
		}

		/**
		 * @inheritdoc
		 */
		public function registerInline(IInline $inline): ICompiler {
			$this->inlineList[$inline->getName()] = $inline;
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
			foreach (NodeIterator::recursive($root = $this->resourceManager->resource($source), true) as $node) {
				foreach ($node->getAttributeList() as $k => $v) {
					if (isset($this->inlineList[$k])) {
						$this->inlineList[$k]->compile($node, $this);
					}
				}
				$this->compile($node);
			}
//			foreach (NodeIterator::recursive($root, true) as $node) {
//				if (isset($this->macroList[$node->getName()])) {
//					continue;
//				}
//				throw new CompilerException(sprintf('Unknown macro [%s].', $node->getPath()));
//			}
			$this->stack->pop();
			return $root;
		}

		/**
		 * @inheritdoc
		 */
		public function compile(INode $macro) {
			return $this->macroList[$macro->getName()]->compile($macro, $this);
		}

		/**
		 * @inheritdoc
		 * @throws CompilerException
		 */
		public function macro(INode $macro) {
			if (isset($this->macroList[$name = $macro->getName()]) === false) {
				throw new CompilerException(sprintf('Unknown macro [%s].', $macro->getPath()));
			}
			if (empty($this->inlineList) === false) {
				foreach ($macro->getAttributeList() as $k => $v) {
					if (isset($this->inlineList[$k])) {
						$this->inlineList[$k]->macro($macro, $this);
						$macro->removeAttribute($k);
					}
				}
			}
			return $this->macroList[$name]->macro($macro, $this);
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
			if (isset($blockList[$name])) {
				throw new MacroException(sprintf('Block id [%s] has been already defined.', $name));
			}
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

		public function registerHelperSet(IHelperSet $helperSet): ICompiler {
			$this->helperSetList[] = $helperSet;
			return $this;
		}
	}

<?php
	declare(strict_types = 1);

	namespace Edde\Common\Template;

	use Edde\Api\Container\IContainer;
	use Edde\Api\Crypt\ICryptEngine;
	use Edde\Api\File\IFile;
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

		public function lazyContainer(IContainer $container) {
			$this->container = $container;
		}

		public function lazyResourceManager(IResourceManager $resourceManager) {
			$this->resourceManager = $resourceManager;
		}

		public function lazyCryptEngine(ICryptEngine $cryptEngine) {
			$this->cryptEngine = $cryptEngine;
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
					$this->compile($import)
						->setMeta('included', true);
				}
				return $this->runtimeMacro($this->compile($this->source));
			} catch (\Exception $exception) {
				/**
				 * Ugly hack to set exception message without messing with a trace.
				 */
				$stackList = [
					$this->source->getPath() => $this->source->getPath(),
				];
				while ($this->stack->isEmpty() === false) {
					$path = $this->stack->pop()
						->getPath();
					$stackList[$path] = $path;
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
		public function compile(IFile $source): INode {
			$this->use();
			$this->stack->push($source);
			foreach (NodeIterator::recursive($root = $this->resourceManager->resource($source), true) as $node) {
				if (empty($this->inlineList) === false) {
					foreach ($node->getAttributeList() as $k => $v) {
						if (isset($this->inlineList[$k]) && $this->inlineList[$k]->isCompile()) {
							$this->inlineList[$k]->macro($node, $this);
						}
					}
				}
				if (isset($this->macroList[$name = $node->getName()])) {
					$this->compileMacro($node);
				}
			}
			foreach (NodeIterator::recursive($root, true) as $node) {
				if (isset($this->macroList[$node->getName()])) {
					continue;
				}
				throw new CompilerException(sprintf('Unknown macro [%s].', $node->getPath()));
			}
			$this->stack->pop();
			return $root;
		}

		/**
		 * @inheritdoc
		 */
		public function compileMacro(INode $macro) {
			if ($this->macroList[$name = $macro->getName()]->isRuntime()) {
				return null;
			}
			return $this->macroList[$name]->macro($macro, $this);
		}

		/**
		 * @inheritdoc
		 * @throws CompilerException
		 */
		public function runtimeMacro(INode $macro) {
			if (isset($this->macroList[$name = $macro->getName()]) === false) {
				throw new CompilerException(sprintf('Unknown macro [%s].', $macro->getPath()));
			}
			if ($this->macroList[$name]->isCompile()) {
				foreach ($macro->getNodeList() as $node) {
					$this->runtimeMacro($node);
				}
				return null;
			}
			if (empty($this->inlineList) === false) {
				foreach ($macro->getAttributeList() as $k => $v) {
					if (isset($this->inlineList[$k]) && $this->inlineList[$k]->isRuntime()) {
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
		public function helper($value) {
			$this->use();
			$result = null;
			foreach ($this->helperSetList as $helperSet) {
				foreach ($helperSet->getHelperList() as $helper) {
					if (($result = $helper->helper($value)) !== null) {
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

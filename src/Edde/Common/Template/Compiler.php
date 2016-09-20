<?php
	declare(strict_types = 1);

	namespace Edde\Common\Template;

	use Edde\Api\Container\IContainer;
	use Edde\Api\File\IFile;
	use Edde\Api\Node\INode;
	use Edde\Api\Resource\IResourceManager;
	use Edde\Api\Template\CompilerException;
	use Edde\Api\Template\ICompiler;
	use Edde\Api\Template\IInline;
	use Edde\Api\Template\IMacro;
	use Edde\Common\Node\NodeIterator;
	use Edde\Common\Usable\AbstractUsable;

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
		 * @var IFile
		 */
		protected $source;
		/**
		 * pre process (compile-time) macros
		 *
		 * @var IMacro[]
		 */
		protected $compileList = [];
		/**
		 * @var IInline[]
		 */
		protected $compileInlineList = [];
		/**
		 * @var IMacro[]
		 */
		protected $macroList = [];
		/**
		 * @var IInline[]
		 */
		protected $macroInlineList = [];
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

		public function registerCompileMacro(IMacro $macro): ICompiler {
			$this->compileList[$macro->getName()] = $macro;
			return $this;
		}

		public function registerCompileInlineMacro(IInline $inline): ICompiler {
			$this->compileInlineList[$inline->getName()] = $inline;
			return $this;
		}

		public function registerMacro(IMacro $macro): ICompiler {
			$this->macroList[$macro->getName()] = $macro;
			return $this;
		}

		public function registerInlineMacro(IInline $inline): ICompiler {
			$this->macroInlineList[$inline->getName()] = $inline;
			return $this;
		}

		public function template(INode $template = null) {
			$this->use();
			$this->context = [];
			try {
				return $this->macro($template ?: $this->compile($this->source));
			} catch (\Exception $exception) {
				$stackList = [];
				while ($this->stack->isEmpty() === false) {
					$stackList[] = $this->stack->pop()
						->getPath();
				}
				throw new CompilerException(sprintf("Template compilation failed: %s\nTemplate file stack:\n%s", $exception->getMessage(), implode(",\n", $stackList)), 0, $exception);
			}
		}

		public function macro(INode $macro) {
			if (isset($this->macroList[$name = $macro->getName()]) === false) {
				throw new CompilerException(sprintf('Unknown macro [%s].', $macro->getPath()));
			}
			if (empty($this->macroInlineList) === false) {
				foreach ($macro->getAttributeList() as $k => $v) {
					if (isset($this->macroInlineList[$k])) {
						$this->macroInlineList[$k]->macro($macro, $this);
						$macro->removeAttribute($k);
					}
				}
			}
			return $this->macroList[$name]->macro($macro, $this);
		}

		public function compile(IFile $source): INode {
			$this->use();
			$this->stack->push($source);
			foreach (NodeIterator::recursive($source = $this->resourceManager->resource($source), true) as $node) {
				if (empty($this->compileInlineList) === false) {
					foreach ($node->getAttributeList() as $k => $v) {
						if (isset($this->compileInlineList[$k])) {
							$this->compileInlineList[$k]->macro($node, $this);
						}
					}
				}
				if (isset($this->compileList[$name = $node->getName()])) {
					$this->execute($node);
				}
			}
			foreach (NodeIterator::recursive($source, true) as $node) {
				if (isset($this->compileList[$node->getName()]) || isset($this->macroList[$node->getName()])) {
					continue;
				}
				throw new CompilerException(sprintf('Unknown macro [%s].', $node->getPath()));
			}

			$this->stack->pop();
			return $source;
		}

		public function execute(INode $macro) {
			return $this->compileList[$name = $macro->getName()]->macro($macro, $this);
		}

		public function getSource(): IFile {
			return $this->source;
		}

		public function getCurrent(): IFile {
			return $this->stack->top();
		}

		public function isLayout(): bool {
			return $this->stack->count() === 1;
		}

		public function setValue(string $name, $value): ICompiler {
			$this->context[$name] = $value;
			return $this;
		}

		public function getValue(string $name, $default = null) {
			if (isset($this->context[$name]) === false) {
				$this->context[$name] = $default;
				return $this->context[$name];
			}
			return $this->context[$name];
		}

		protected function prepare() {
			$this->stack = new \SplStack();
		}
	}

<?php
	declare(strict_types = 1);

	namespace Edde\Common\Template;

	use Edde\Api\Container\IContainer;
	use Edde\Api\File\IFile;
	use Edde\Api\Node\INode;
	use Edde\Api\Resource\IResourceManager;
	use Edde\Api\Template\CompilerException;
	use Edde\Api\Template\ICompiler;
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
		 * @var IMacro[]
		 */
		protected $macroList = [];
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

		public function registerMacro(IMacro $macro): ICompiler {
			$this->macroList[$macro->getName()] = $macro;
			return $this;
		}

		public function template(INode $template = null) {
			$this->use();
			$this->context = [];
			$template = $template ?: $this->compile($this->source);
			return $this->macroList[$name = $template->getName()]->macro($template, $this);
		}

		public function compile(IFile $source): INode {
			$this->use();
			$this->stack->push($source);
			foreach (NodeIterator::recursive($source = $this->resourceManager->resource($source), true) as $node) {
				if (isset($this->compileList[$name = $node->getName()])) {
					$this->compileList[$name]->macro($node, $this);
				}
			}
			foreach (NodeIterator::recursive($source) as $node) {
				if (isset($this->compileList[$node->getName()]) || isset($this->macroList[$node->getName()])) {
					continue;
				}
				if ($this->stack->count() <= 1) {
					throw new CompilerException(sprintf('Unknown macro [%s] in template [%s].', $node->getPath(), $this->source->getPath()));
				}
				throw new CompilerException(sprintf('Unknown macro [%s] in template [%s] of root template [%s].', $node->getPath(), $this->stack->top()
					->getPath(), $this->source->getPath()));
			}
			$this->stack->pop();
			return $source;
		}

		public function getSource(): IFile {
			return $this->source;
		}

		public function getCurrent(): IFile {
			return $this->stack->top();
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

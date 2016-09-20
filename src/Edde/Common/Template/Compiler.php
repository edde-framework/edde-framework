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
		 * @var IFile
		 */
		protected $destination;
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
		 * @param IFile $source
		 * @param IFile $destination
		 */
		public function __construct(IFile $source, IFile $destination) {
			$this->source = $source;
			$this->destination = $destination;
		}

		public function lazyContainer(IContainer $container) {
			$this->container = $container;
		}

		public function lazyResourceManager(IResourceManager $resourceManager) {
			$this->resourceManager = $resourceManager;
		}

		public function registerCompileMacro(IMacro $macro): ICompiler {
			$this->addMacro($macro, $this->compileList);
			return $this;
		}

		protected function addMacro(IMacro $macro, array &$target) {
			foreach ($macro->getMacroList() as $name) {
				if (isset($this->compileList[$name])) {
					throw new CompilerException(sprintf('Cannot register macro [%s]; the name [%s] was already registered by macro [%s].', get_class($macro), $name, get_class($this->compileList[$name])));
				}
				$target[$name] = $macro;
			}
		}

		public function registerMacro(IMacro $macro): ICompiler {
			$this->addMacro($macro, $this->macroList);
			return $this;
		}

		public function template(INode $template = null) {
			$this->use();
			$template = $template ?: $this->compile();
			if (isset($this->macroList[$name = $template->getName()]) === false) {
				throw new CompilerException(sprintf('Unknown macro [%s] in template [%s].', $template->getPath(), $this->source->getPath()));
			}
			return $this->macroList[$name]->macro($template, $this);
		}

		public function compile(): INode {
			$this->use();
			foreach (NodeIterator::recursive($source = $this->resourceManager->resource($this->source), true) as $node) {
				if (isset($this->compileList[$name = $node->getName()])) {
					$this->compileList[$name]->macro($node, $this);
				}
			}
			return $source;
		}

		protected function prepare() {
		}
	}

<?php
	declare(strict_types = 1);

	namespace Edde\Common\Template\Macro;

	use Edde\Api\Node\INode;
	use Edde\Api\Resource\IResourceManager;
	use Edde\Api\Template\ICompiler;
	use Edde\Common\Container\LazyInjectTrait;
	use Edde\Common\Node\Node;
	use Edde\Common\Template\AbstractMacro;

	class IncludeMacro extends AbstractMacro {
		use LazyInjectTrait;

		/**
		 * @var IResourceManager
		 */
		protected $resourceManager;

		public function __construct() {
			parent::__construct([
				'include',
				'm:include',
			]);
		}

		public function lazyResourceManager(IResourceManager $resourceManager) {
			$this->resourceManager = $resourceManager;
		}

		public function run(INode $root, ICompiler $compiler) {
			switch ($root->getName()) {
				case 'include':
					$node = new Node();
					$include = $compiler->file($root->getAttribute('src'));
					$compiler->getDestination()
						->write(sprintf("\t\t\t/** include %s */\n", $include));
					$node->setNodeList($this->resourceManager->file($include)
						->getNodeList(), true);
					$this->macro($node, $compiler);
					$compiler->getDestination()
						->write(sprintf("\t\t\t/** done %s */\n", $include));
					break;
				case 'm:include':
					$include = $compiler->file($root->getValue());
					$compiler->getDestination()
						->write(sprintf("\t\t\t/** include %s */\n", $include));
					$root->getNodeList()[0]->setNodeList($this->resourceManager->file($include)
						->getNodeList(), true);
					$this->macro($root, $compiler);
					$compiler->getDestination()
						->write(sprintf("\t\t\t/** done %s */\n", $include));
					break;
			}
		}
	}

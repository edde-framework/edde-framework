<?php
	declare(strict_types = 1);

	namespace Edde\Common\Template\Macro;

	use Edde\Api\Node\INode;
	use Edde\Api\Resource\IResourceManager;
	use Edde\Api\Template\ICompiler;
	use Edde\Common\Node\Node;
	use Edde\Common\Template\AbstractMacro;

	class IncludeMacro extends AbstractMacro {
		/**
		 * @var IResourceManager
		 */
		protected $resourceManager;

		/**
		 * @param IResourceManager $resourceManager
		 */
		public function __construct(IResourceManager $resourceManager) {
			parent::__construct([
				'include',
				'm:include',
			]);
			$this->resourceManager = $resourceManager;
		}

		public function run(INode $root, ICompiler $compiler) {
			switch ($root->getName()) {
				case 'include':
					$node = new Node();
					$node->setNodeList($this->resourceManager->file($compiler->file($root->getAttribute('src')))
						->getNodeList(), true);
					$this->macro($node, $compiler);
					break;
				case 'm:include':
					$root->getNodeList()[0]->setNodeList($this->resourceManager->file($compiler->file($root->getValue()))
						->getNodeList(), true);
					$this->macro($root, $compiler);
					break;
			}
		}
	}

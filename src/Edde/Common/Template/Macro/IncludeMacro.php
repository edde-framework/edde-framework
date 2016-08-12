<?php
	declare(strict_types = 1);

	namespace Edde\Common\Template\Macro;

	use Edde\Api\Node\INode;
	use Edde\Api\Resource\IResource;
	use Edde\Api\Resource\IResourceManager;
	use Edde\Api\Template\ITemplate;
	use Edde\Api\Template\ITemplateManager;
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
			parent::__construct(['m:include']);
			$this->resourceManager = $resourceManager;
		}

		public function run(ITemplateManager $templateManager, ITemplate $template, INode $root, IResource $resource, ...$parameterList) {
			$include = $root->getValue();
			if ($include[0] !== '/') {
				$include = $resource->getUrl()
						->getBasePath() . '/' . $include;
			}
			$root->getNodeList()[0]->setNodeList($this->resourceManager->file($include)
				->getNodeList(), true);
		}
	}

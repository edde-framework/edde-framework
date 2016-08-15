<?php
	declare(strict_types = 1);

	namespace Edde\Common\Template\Macro;

	use Edde\Api\File\IFile;
	use Edde\Api\Node\INode;
	use Edde\Api\Resource\IResourceManager;
	use Edde\Api\Template\ITemplate;
	use Edde\Api\Template\ITemplateManager;
	use Edde\Common\Template\AbstractMacro;

	class IncludeNodeMacro extends AbstractMacro {
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

		public function run(ITemplateManager $templateManager, ITemplate $template, INode $root, IFile $file, ...$parameterList) {
			$root->getNodeList()[0]->setNodeList($this->resourceManager->file($this->file($root->getValue(), $file))
				->getNodeList(), true);
		}
	}

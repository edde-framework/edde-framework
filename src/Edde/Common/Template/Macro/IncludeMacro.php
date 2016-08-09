<?php
	declare(strict_types = 1);

	namespace Edde\Common\Template\Macro;

	use Edde\Api\Node\INode;
	use Edde\Api\Resource\IResourceManager;
	use Edde\Api\Template\ITemplate;
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
			parent::__construct(['include']);
			$this->resourceManager = $resourceManager;
		}

		public function run(ITemplate $template, INode $root, ...$parameterList) {
			$file = $root->getAttribute('file');
			if ($file[0] === '$') {
				$file = $template->getVariable(ltrim($file, '$'));
			}
			foreach ($this->resourceManager->file($file)
				         ->getNodeList() as $node) {
				$template->macro($node, ...$parameterList);
			}
		}
	}

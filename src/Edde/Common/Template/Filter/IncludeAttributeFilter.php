<?php
	declare(strict_types = 1);

	namespace Edde\Common\Template\Filter;

	use Edde\Api\Html\IHtmlControl;
	use Edde\Api\Node\INode;
	use Edde\Api\Resource\IResourceManager;
	use Edde\Api\Template\ITemplate;
	use Edde\Common\Filter\AbstractFilter;

	class IncludeAttributeFilter extends AbstractFilter {
		/**
		 * @var IResourceManager
		 */
		protected $resourceManager;

		/**
		 * @param IResourceManager $resourceManager
		 */
		public function __construct(IResourceManager $resourceManager) {
			$this->resourceManager = $resourceManager;
		}

		public function input($value, IHtmlControl $htmlControl, INode $root, ITemplate $template) {
			$file = $value;
			if ($file[0] === '$') {
				$file = $template->getVariable(ltrim($file, '$'));
			}
			foreach ($this->resourceManager->file($file)
				         ->getNodeList() as $node) {
				$template->macro($node, $htmlControl);
			}
			return false;
		}
	}

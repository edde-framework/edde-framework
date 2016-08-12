<?php
	declare(strict_types = 1);
	namespace Edde\Common\Template;

	use Edde\Api\Node\INode;
	use Edde\Api\Resource\IResource;
	use Edde\Api\Template\IMacro;
	use Edde\Api\Template\ITemplate;
	use Edde\Api\Template\ITemplateManager;
	use Edde\Common\AbstractObject;

	abstract class AbstractMacro extends AbstractObject implements IMacro {
		/**
		 * @var array
		 */
		protected $macroList = [];

		/**
		 * @param array $macroList
		 */
		public function __construct(array $macroList) {
			$this->macroList = $macroList;
		}

		public function getMacroList(): array {
			return $this->macroList;
		}

		protected function macro(INode $root, ITemplateManager $templateManager, ITemplate $template, IResource $resource, ...$parameterList) {
			foreach ($root->getNodeList() as $node) {
				$templateManager->macro($node, $template, $resource, ...$parameterList);
			}
		}
	}

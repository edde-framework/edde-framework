<?php
	declare(strict_types = 1);

	namespace Edde\Common\Template\Macro;

	use Edde\Api\Html\IHtmlControl;
	use Edde\Api\Node\INode;
	use Edde\Api\Template\ITemplate;
	use Edde\Common\Template\AbstractMacro;

	/**
	 * Html (root) control macro for processing html based controls.
	 */
	class HtmlMacro extends AbstractMacro {
		public function __construct() {
			parent::__construct(['html']);
		}

		public function execute(ITemplate $template, INode $root, IHtmlControl $htmlControl) {
			foreach ($root->getNodeList() as $node) {
				$template->macro($node, $htmlControl);
			}
			return $htmlControl;
		}
	}

<?php
	declare(strict_types=1);

	namespace Edde\Common\Template\Macro;

	use Edde\Api\Node\INode;
	use Edde\Api\Template\ITemplate;
	use Edde\Common\Template\AbstractMacro;

	class IncludeMacro extends AbstractMacro {
		public function getNameList(): array {
			return ['include'];
		}

		public function inline(ITemplate $template, \Iterator $iterator, INode $node, $value = null) {
		}
	}

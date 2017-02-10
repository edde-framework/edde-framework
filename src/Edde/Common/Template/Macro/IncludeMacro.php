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

		public function inline(ITemplate $template, INode $node, string $name, string $value = null) {
			$block = clone $template->getBlock($value, $node);
			$metaList = $block->getMetaList();
			if ($metaList->get('block-root', false)) {
				$node->addNodeList($block->getNodeList(), true);
				return;
			}
			$node->addNode($block);
		}
	}

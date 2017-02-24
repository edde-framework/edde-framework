<?php
	declare(strict_types=1);

	namespace Edde\Common\Template\Macro;

	use Edde\Api\Node\INode;
	use Edde\Api\Template\ITemplate;
	use Edde\Common\Template\AbstractMacro;

	class SnippetMacro extends AbstractMacro {
		/**
		 * @inheritdoc
		 */
		public function getNameList(): array {
			return ['snippet'];
		}

		/**
		 * @inheritdoc
		 */
		public function inline(ITemplate $template, INode $node, string $name, string $value = null) {
			$metaList = $node->getMetaList();
			$metaList->set('block-root', true);
			$metaList->set('skip', true);
			$template->block($value, $node);
		}

		/**
		 * @inheritdoc
		 */
		public function macro(ITemplate $template, INode $node) {
			$attributeList = $node->getAttributeList();
			$metaList = $node->getMetaList();
			$metaList->set('skip', true);
			$template->block((string)$attributeList->get('name'), $node);
			$metaList->set('block-root', false);
		}
	}

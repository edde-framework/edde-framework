<?php
	declare(strict_types=1);

	namespace Edde\Common\Template\Macro;

	use Edde\Api\Node\INode;
	use Edde\Api\Template\ITemplate;
	use Edde\Api\Template\LazyTemplateDirectoryTrait;
	use Edde\Common\Template\AbstractMacro;

	class SnippetMacro extends AbstractMacro {
		use LazyTemplateDirectoryTrait;

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
		}

		/**
		 * @inheritdoc
		 */
		public function enter(INode $node, ...$parameters) {
			ob_start();
		}

		/**
		 * @inheritdoc
		 */
		public function leave(INode $node, ...$parameters) {
			$this->templateDirectory->save($this->getSnippetFile($node), ob_get_clean());
		}

		protected function getSnippetFile(INode $node) {
			$attributeList = $node->getAttributeList();
			return 'snippet-' . sha1($attributeList->get('name') . '.php');
		}
	}

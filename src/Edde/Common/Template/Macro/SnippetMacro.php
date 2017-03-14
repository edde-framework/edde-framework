<?php
	declare(strict_types=1);

	namespace Edde\Common\Template\Macro;

	use Edde\Api\Node\INode;
	use Edde\Api\Template\ITemplate;
	use Edde\Api\Template\LazyTemplateDirectoryTrait;
	use Edde\Common\Node\SkipException;
	use Edde\Common\Strings\StringUtils;
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
		public function inline(ITemplate $template, \Iterator $iterator, INode $node, $value = null) {
			ob_start();
			$macro = $this->traverse($node, $template);
			$iterator->next();
			$macro->enter($node, $iterator, $template);
			$macro->node($node, $iterator, $template);
			$macro->leave($node, $iterator, $template);
			$this->templateDirectory->save($this->getSnippetFile($node, $value), ob_get_clean());
			throw new SkipException();
		}

		/**
		 * @inheritdoc
		 */
		public function enter(INode $node, \Iterator $iterator, ...$parameters) {
			ob_start();
		}

		/**
		 * @inheritdoc
		 */
		public function leave(INode $node, \Iterator $iterator, ...$parameters) {
			$this->templateDirectory->save($this->getSnippetFile($node), ob_get_clean());
		}

		/**
		 * compute snippet filename
		 *
		 * @param INode       $node
		 * @param string|null $name
		 *
		 * @return string
		 */
		protected function getSnippetFile(INode $node, string $name = null): string {
			$attributeList = $node->getAttributeList();
			return 'snippet-' . StringUtils::webalize($name ?? (string)$attributeList->get('name')) . '.php';
		}
	}

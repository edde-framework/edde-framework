<?php
	declare(strict_types=1);

	namespace Edde\Common\Template\Macro;

	use Edde\Api\Html\LazyHtmlGeneratorTrait;
	use Edde\Api\Node\INode;
	use Edde\Common\Template\AbstractMacro;

	class HtmlMacro extends AbstractMacro {
		use LazyHtmlGeneratorTrait;

		/**
		 * @inheritdoc
		 */
		public function getNameList(): array {
			return $this->htmlGenerator->getTagList();
		}

		/**
		 * @inheritdoc
		 */
		public function onEnter(INode $node, \Iterator $iterator, ...$parameters) {
			echo $this->htmlGenerator->open($node);
		}

		/**
		 * @inheritdoc
		 */
		public function onNode(INode $node, \Iterator $iterator, ...$parameters) {
			if (($content = $this->htmlGenerator->content($node)) !== '') {
				echo $content;
				return;
			}
			parent::onNode($node, $iterator, ...$parameters);
		}

		/**
		 * @inheritdoc
		 */
		public function onLeave(INode $node, \Iterator $iterator, ...$parameters) {
			echo $this->htmlGenerator->close($node) . "\n";
		}
	}

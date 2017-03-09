<?php
	declare(strict_types=1);

	namespace Edde\Common\Template\Macro;

	use Edde\Api\Html\LazyHtmlGeneratorTrait;
	use Edde\Api\Node\INode;
	use Edde\Api\Template\ITemplate;
	use Edde\Common\Template\AbstractMacro;

	class HtmlMacro extends AbstractMacro {
		use LazyHtmlGeneratorTrait;

		/**
		 * @inheritdoc
		 */
		public function getNameList(): array {
			return $this->htmlGenerator->getTagList();
		}

		public function open(ITemplate $template, INode $node) {
			echo sprintf("%s", $this->htmlGenerator->open($node));
		}

		public function close(ITemplate $template, INode $node) {
			echo sprintf("%s\n", $this->htmlGenerator->close($node));
		}
	}

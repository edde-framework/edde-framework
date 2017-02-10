<?php
	declare(strict_types=1);

	namespace Edde\Common\Template\Macro;

	use Edde\Api\Html\LazyHtmlGeneratorTrait;
	use Edde\Common\Template\AbstractMacro;

	class HtmlMacro extends AbstractMacro {
		use LazyHtmlGeneratorTrait;

		/**
		 * @inheritdoc
		 */
		public function getNameList(): array {
			return $this->htmlGenerator->getTagList();
		}
	}

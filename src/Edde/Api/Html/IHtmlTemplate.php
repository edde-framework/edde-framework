<?php
	declare(strict_types = 1);

	namespace Edde\Api\Html;

	use Edde\Api\Template\ITemplate;

	interface IHtmlTemplate extends ITemplate {
		/**
		 * @param IHtmlControl $root
		 * @param string|null $snippet
		 *
		 * @return IHtmlControl input root is also output
		 */
		public function snippet(IHtmlControl $root, string $snippet = null): IHtmlControl;
	}

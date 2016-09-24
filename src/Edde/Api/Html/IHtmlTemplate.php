<?php
	declare(strict_types = 1);

	namespace Edde\Api\Html;

	use Edde\Api\Template\ITemplate;

	/**
	 * Formal interface for all html based templates (html control package).
	 */
	interface IHtmlTemplate extends ITemplate {
		/**
		 * @param IHtmlControl $root
		 * @param string|null $snippet
		 *
		 * @return IHtmlControl input root is also output
		 */
		public function snippet(IHtmlControl $root, string $snippet = null): IHtmlControl;

		/**
		 * return list of defined blocks in this template
		 *
		 * @return array
		 */
		public function getBlockList(): array;
	}

<?php
	declare(strict_types = 1);

	namespace Edde\Api\Template;

	use Edde\Api\Html\IHtmlControl;

	interface ITemplate {
		/**
		 * @return IHtmlControl
		 */
		public function getControl(): IHtmlControl;

		/**
		 * @return string
		 */
		public function getFile(): string;
	}

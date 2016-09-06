<?php
	declare(strict_types = 1);

	namespace Edde\Api\Html;

	use Edde\Api\Control\IControl;

	interface IHtmlTemplate {
		public function include (string $file);

		public function template(IControl $root);

		/**
		 * return array of lambdas for controls
		 *
		 * @return array
		 */
		public function getControlList(): array;
	}

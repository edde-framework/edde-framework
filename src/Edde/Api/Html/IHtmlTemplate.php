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

		/**
		 * apply named block on root control
		 *
		 * @param string $name
		 * @param IControl $root
		 *
		 * @return IHtmlTemplate
		 */
		public function control(string $name, IControl $root): IHtmlTemplate;
	}

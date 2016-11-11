<?php
	declare(strict_types = 1);

	namespace Edde\Api\Html;

	/**
	 * Formal root implementation for a html page/fragment.
	 */
	interface IHtmlView extends IHtmlControl {
		/**
		 * helper method for html control creation
		 *
		 * @param string $control
		 * @param array ...$parameterList
		 *
		 * @return IHtmlControl
		 */
		public function createControl(string $control, ...$parameterList): IHtmlControl;

		/**
		 * send response to the current request
		 */
		public function response();

		/**
		 * @param $redirect
		 */
		public function redirect($redirect);
	}

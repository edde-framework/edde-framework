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
		 * add the given control as a snippet
		 *
		 * @param IHtmlControl $htmlControl
		 * @param callable $callback
		 *
		 * @return IHtmlView
		 */
		public function snippet(IHtmlControl $htmlControl, callable $callback = null): IHtmlView;

		/**
		 * return array of "dirty" snippets
		 *
		 * @param bool $force
		 *
		 * @return array|IHtmlControl[]
		 */
		public function snippets($force = false): array;

		/**
		 * send response to the current request
		 *
		 * @return IHtmlView
		 */
		public function response(): IHtmlView;
	}

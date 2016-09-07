<?php
	declare(strict_types = 1);

	namespace Edde\Api\Html;

	use Edde\Api\Control\IControl;
	use Edde\Api\Template\ITemplate;

	interface IHtmlTemplate extends ITemplate {
		/**
		 * build up a target control with this template
		 *
		 * @return IHtmlTemplate
		 */
		public function template(): IHtmlTemplate;

		/**
		 * add a dependant template file
		 *
		 * @param string[] $importList
		 *
		 * @return IHtmlTemplate
		 */
		public function import(...$importList): IHtmlTemplate;

		/**
		 * return array of lambdas for controls
		 *
		 * @return callable[]
		 */
		public function getControlList(): array;

		/**
		 * apply named block on root control
		 *
		 * @param string $name
		 * @param IControl $root
		 *
		 * @return IControl
		 */
		public function control(string $name, IControl $root): IControl;
	}

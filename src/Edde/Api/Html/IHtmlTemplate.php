<?php
	declare(strict_types = 1);

	namespace Edde\Api\Html;

	use Edde\Api\Control\IControl;
	use Edde\Api\Template\ITemplate;

	interface IHtmlTemplate extends ITemplate {
		/**
		 * build up a target control with this template
		 *
		 * @param array $importList another templates required by this one
		 *
		 * @return IHtmlTemplate
		 */
		public function template(array $importList = []): IHtmlTemplate;

		/**
		 * add a dependant template file
		 *
		 * @param string $file
		 *
		 * @return IHtmlTemplate
		 */
		public function import(string $file): IHtmlTemplate;

		/**
		 * @param string $id
		 * @param callable $callback
		 * @param bool $force
		 *
		 * @return IHtmlTemplate
		 */
		public function addControl($id, callable $callback, bool $force = false): IHtmlTemplate;

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

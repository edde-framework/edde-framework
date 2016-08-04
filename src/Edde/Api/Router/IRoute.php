<?php
	declare(strict_types = 1);

	namespace Edde\Api\Router;

	use Edde\Api\Crate\ICrate;

	/**
	 * Route is path to the system service/control/who-will-handle-a-given-request.
	 */
	interface IRoute {
		/**
		 * target handling class
		 *
		 * @return string
		 */
		public function getClass();

		/**
		 * target method (must be public as it should be called)
		 *
		 * @return string
		 */
		public function getMethod();

		/**
		 * list of parameter which should be pased to the method; they are intended to be simple
		 * array; more complex data should be transfered via Crate
		 *
		 * @return array
		 */
		public function getParameterList();

		/**
		 * @return ICrate[]
		 */
		public function getCrateList();
	}

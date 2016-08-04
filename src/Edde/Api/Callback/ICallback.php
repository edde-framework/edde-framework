<?php
	declare(strict_types = 1);

	namespace Edde\Api\Callback;

	/**
	 * Encapsulation for any callable.
	 */
	interface ICallback {
		/**
		 * @return callable
		 */
		public function getCallback();

		/**
		 * return array of dependencies (parameter list)
		 *
		 * @return IParameter[]
		 */
		public function getParameterList();

		/**
		 * @param array ...$parameterList
		 *
		 * @return mixed
		 */
		public function invoke(...$parameterList);

		/**
		 * @param array $parameterList
		 *
		 * @return mixed
		 */
		public function __invoke(...$parameterList);
	}

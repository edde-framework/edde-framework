<?php
	declare(strict_types = 1);

	namespace Edde\Api\Application;

	/**
	 * General application request (it should not be necessarily be handled by an applicaiton).
	 */
	interface IRequest {
		/**
		 * "mime" type (it can be arbitrary string) of a request
		 *
		 * @return string
		 */
		public function getType(): string;

		/**
		 * target class to call; here is no constraint, but it is recommanded to allow only instances of control
		 *
		 * @return string
		 */
		public function getClass(): string;

		/**
		 * target method; this string hould be "final" (so no further computations would be done)
		 *
		 * @return string
		 */
		public function getMethod(): string;

		/**
		 * the given method should be called with this parameter list
		 *
		 * @return array
		 */
		public function getParameterList(): array;
	}

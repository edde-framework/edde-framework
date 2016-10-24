<?php
	declare(strict_types = 1);

	namespace Edde\Api\Application;

	/**
	 * General application request (it should not be necessarily be handled by an application).
	 */
	interface IRequest {
		/**
		 * register handler; the order is important (for example context, handle, ...)
		 *
		 * @param string $class
		 * @param string $method
		 *
		 * @return IRequest
		 */
		public function registerHandler(string $class, string $method): IRequest;

		/**
		 * @return array
		 */
		public function getHandlerList(): array;

		/**
		 * return the name of current handle (first of handler list)
		 *
		 * @return string
		 */
		public function getCurrentHandle(): string;

		/**
		 * "mime" type (it can be arbitrary string) of a request
		 *
		 * @return string
		 */
		public function getType(): string;

		/**
		 * the given method should be called with this parameter list
		 *
		 * @return array
		 */
		public function getParameterList(): array;

		/**
		 * return id based on current handler list
		 *
		 * @return string
		 */
		public function getId(): string;
	}

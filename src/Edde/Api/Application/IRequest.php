<?php
	declare(strict_types = 1);

	namespace Edde\Api\Application;

	/**
	 * General application request (it should not be necessarily be handled by an application).
	 */
	interface IRequest {
		/**
		 * "mime" type (it can be arbitrary string) of a request
		 *
		 * @return string
		 */
		public function getType(): string;

		/**
		 * register an action handler (target control and target method to be called)
		 *
		 * @param string $control
		 * @param string $action
		 * @param array $parameterList
		 *
		 * @return IRequest
		 */
		public function registerActionHandler(string $control, string $action, array $parameterList = []): IRequest;

		/**
		 * has this request action
		 *
		 * @return bool
		 */
		public function hasAction(): bool;

		/**
		 * return an action name (this should be executed)
		 *
		 * @return string[] return [control, method, parameter list]
		 */
		public function getAction(): array;

		/**
		 * register a target control to handle "handle"
		 *
		 * @param string $control
		 * @param string $handle
		 * @param array $parameterList
		 *
		 * @return IRequest
		 */
		public function registerHandleHandler(string $control, string $handle, array $parameterList = []): IRequest;

		/**
		 * should be handler executed?
		 *
		 * @return bool
		 */
		public function hasHandle(): bool;

		/**
		 * return handle name to be executed
		 *
		 * @return string[] return [control, method, parameter list]
		 */
		public function getHandle(): array;

		/**
		 * return current call name (action/handle); handle should have a precedence
		 *
		 * @return string[] return [control, method, parameter list]
		 */
		public function getCall(): array;

		/**
		 * return request id
		 *
		 * @return string
		 */
		public function getId(): string;
	}

<?php
	declare(strict_types=1);

	namespace Edde\Api\Application;

	use Edde\Api\Container\IConfigurable;

	/**
	 * Response manager holds current Response (to keep responses immutable).
	 */
	interface IResponseManager extends IConfigurable {
		/**
		 * set the current response
		 *
		 * @param IResponse $response
		 *
		 * @return IResponseManager
		 */
		public function response(IResponse $response = null): IResponseManager;

		/**
		 * if a response is not set, internal default should be applied or empty response should be returned
		 *
		 * @param array $targetList array of target types in order of precedence
		 *
		 * @return IResponseManager
		 */
		public function setTarget(array $targetList): IResponseManager;

		/**
		 * return target mime type of request (it can be echoing, json_encoding, ...)
		 *
		 * @return string[]
		 */
		public function getTarget(): array ;

		/**
		 * execute response
		 *
		 * @return mixed
		 */
		public function execute();
	}

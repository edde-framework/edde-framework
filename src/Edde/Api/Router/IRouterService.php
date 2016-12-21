<?php
	declare(strict_types = 1);

	namespace Edde\Api\Router;

	use Edde\Api\Application\IRequest;

	/**
	 * Implementation of application router service.
	 */
	interface IRouterService extends IRouterList {
		/**
		 * @return IRequest
		 */
		public function createRequest(): IRequest;
	}

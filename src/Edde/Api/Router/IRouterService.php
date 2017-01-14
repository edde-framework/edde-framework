<?php
	declare(strict_types = 1);

	namespace Edde\Api\Router;

	use Edde\Api\Application\IRequest;
	use Edde\Api\Container\IConfigurable;

	/**
	 * Implementation of application router service.
	 */
	interface IRouterService extends IRouterList, IConfigurable {
		/**
		 * @return IRequest
		 */
		public function createRequest(): IRequest;
	}

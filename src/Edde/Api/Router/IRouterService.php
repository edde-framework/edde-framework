<?php
	declare(strict_types=1);

	namespace Edde\Api\Router;

	use Edde\Api\Application\IRequest;
	use Edde\Api\Container\IConfigurable;

	/**
	 * Implementation of application router service.
	 */
	interface IRouterService extends IConfigurable {
		/**
		 * routers should be created on demand
		 *
		 * @param string $router
		 * @param array  $parameterList
		 *
		 * @return IRouterService
		 */
		public function registerRouter(string $router, array $parameterList = []): IRouterService;

		/**
		 * @return IRequest
		 */
		public function createRequest(): IRequest;
	}

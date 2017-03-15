<?php
	declare(strict_types=1);

	namespace Edde\Api\Router;

	use Edde\Api\Application\IRequest;
	use Edde\Api\Config\IConfigurable;

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
		 * when routers fail, execute this default request
		 *
		 * @param IRequest $request
		 *
		 * @return IRouterService
		 */
		public function setDefaultRequest(IRequest $request): IRouterService;

		/**
		 * @return IRequest
		 */
		public function createRequest(): IRequest;
	}

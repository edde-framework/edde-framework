<?php
	declare(strict_types = 1);

	namespace Edde\Api\Router;

	interface IRouterService extends IRouter {
		/**
		 * register the given router to a service
		 *
		 * @param IRouter $router
		 *
		 * @return IRouterService
		 */
		public function registerRouter(IRouter $router): IRouterService;
	}

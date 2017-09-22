<?php
	declare(strict_types=1);

	namespace Edde\Api\Router;

	/**
	 * This service is responsible for user to application request translation; because
	 * whole application is build around "The Protocol", result should be packet to be
	 * executed by protocol service.
	 */
	interface IRouterService extends IRouter {
		/**
		 * direct router registration; use wisely as this requires target router to be already instantiated
		 *
		 * @param IRouter $router
		 *
		 * @return IRouterService
		 */
		public function registerRouter(IRouter $router): IRouterService;

		/**
		 * @param array $routerList
		 *
		 * @return IRouterService
		 */
		public function registerRouterList(array $routerList): IRouterService;
	}

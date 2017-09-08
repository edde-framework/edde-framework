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
		 * when no other router catches a request, this one should be executed
		 *
		 * @param IRouter $router
		 *
		 * @return IRouterService
		 */
		public function registerDefaultRouter(IRouter $router): IRouterService;

		/**
		 * when there is an exception, this router should be executed
		 *
		 * @param IRouter $router
		 *
		 * @return IRouterService
		 */
		public function registerErrorRouter(IRouter $router): IRouterService;
	}

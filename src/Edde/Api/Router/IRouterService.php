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
		 * register a router proxy (to deffer router creation only when it's needed)
		 *
		 * @param IRouterProxy $routerProxy
		 *
		 * @return IRouterService
		 */
		public function registerRouterProxy(IRouterProxy $routerProxy): IRouterService;

		/**
		 * when no other router catches a request, this one should be executed
		 *
		 * @param IRouterProxy $routerProxy
		 *
		 * @return IRouterService
		 */
		public function registerDefaultRouterProxy(IRouterProxy $routerProxy): IRouterService;

		/**
		 * when there is an exception, this router should be executed
		 *
		 * @param IRouterProxy $routerProxy
		 *
		 * @return IRouterService
		 */
		public function registerErrorRouterProxy(IRouterProxy $routerProxy): IRouterService;
	}

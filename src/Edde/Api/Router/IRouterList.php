<?php
	declare(strict_types = 1);

	namespace Edde\Api\Router;

	interface IRouterList extends IRouter {
		/**
		 * register the given router
		 *
		 * @param IRouter $router
		 *
		 * @return IRouterList
		 */
		public function registerRouter(IRouter $router): IRouterList;
	}

<?php
	declare(strict_types=1);

	namespace Edde\Api\Router;

	use Edde\Api\Container\IProxy;

	/**
	 * Router proxy, because motherfuckers does not support generics :|.
	 */
	interface IRouterProxy extends IProxy {
		/**
		 * @return IRouter
		 */
		public function proxy(): IRouter;
	}

<?php
	namespace Edde\Common\Router;

	use Edde\Api\Router\IRouter;
	use Edde\Common\Usable\AbstractUsable;

	class RouterList extends AbstractUsable implements IRouter {
		/**
		 * @var IRouter[]
		 */
		private $routerList = [];

		public function registerRouter(IRouter $router) {
			$this->routerList[] = $router;
			return $this;
		}

		public function route() {
			$this->usse();
			foreach ($this->routerList as $router) {
				$router->usse();
				if (($route = $router->route()) !== null) {
					return $route;
				}
			}
			return null;
		}

		protected function prepare() {
		}
	}

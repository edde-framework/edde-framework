<?php
	declare(strict_types = 1);

	namespace Edde\Common\Router;

	use Edde\Api\Router\IRouter;
	use Edde\Common\Usable\AbstractUsable;

	class RouterList extends AbstractUsable implements IRouter {
		/**
		 * @var IRouter[]
		 */
		protected $routerList = [];

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

<?php
	declare(strict_types = 1);

	namespace Edde\Common\Router;

	use Edde\Api\Router\IRouterService;
	use Edde\Api\Router\RouterException;

	class RouterService extends RouterList implements IRouterService {
		public function route() {
			$this->use();
			if (($route = parent::route()) === null) {
				throw new RouterException(sprintf('Cannot find route for current application request.'));
			}
			return $route;
		}
	}

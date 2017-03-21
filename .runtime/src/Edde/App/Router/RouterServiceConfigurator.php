<?php
	declare(strict_types=1);

	namespace Edde\App\Router;

	use Edde\Api\Router\IRouterService;
	use Edde\App\Index\IndexView;
	use Edde\Common\Application\Request;
	use Edde\Ext\Router\RestRouter;
	use Edde\Ext\Router\RouterServiceConfigurator as ExtRouterServiceConfigHandler;
	use Edde\Ext\Router\SimpleHttpRouter;

	class RouterServiceConfigurator extends ExtRouterServiceConfigHandler {
		/**
		 * @param IRouterService $instance
		 */
		public function config($instance) {
			parent::config($instance);
			$instance->setDefaultRequest(new Request(IndexView::class, 'actionIndex'));
			$instance->registerRouter(SimpleHttpRouter::class, [['Edde\\App']]);
			$instance->registerRouter(RestRouter::class);
		}
	}

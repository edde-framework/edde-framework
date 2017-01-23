<?php
	declare(strict_types=1);

	namespace Edde\App\Router;

	use Edde\Ext\Router\RestRouter;
	use Edde\Ext\Router\RouterServiceConfigurator as ExtRouterServiceConfigHandler;
	use Edde\Ext\Router\SimpleHttpRouter;

	class RouterServiceConfigurator extends ExtRouterServiceConfigHandler {
		public function config($instance) {
			parent::config($instance);
			$instance->registerRouter(SimpleHttpRouter::class, [['Edde\\App']]);
			$instance->registerRouter(RestRouter::class);
		}
	}

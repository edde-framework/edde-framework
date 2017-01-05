<?php
	declare(strict_types = 1);

	namespace Edde\App\Router;

	use Edde\Ext\Router\RouterServiceConfigHandler as ExtRouterServiceConfigHandler;
	use Edde\Ext\Router\SimpleHttpRouter;

	class RouterServiceConfigHandler extends ExtRouterServiceConfigHandler {
		public function config($instance) {
			parent::config($instance);
			$instance->registerRouter($this->container->create(SimpleHttpRouter::class, [['Edde\\App']], __METHOD__));
		}
	}

<?php
	declare(strict_types=1);

	namespace Edde\App\Router;

	use Edde\App\Rest\UserService;
	use Edde\Ext\Router\RestRouter;
	use Edde\Ext\Router\RouterServiceConfigHandler as ExtRouterServiceConfigHandler;
	use Edde\Ext\Router\SimpleHttpRouter;

	class RouterServiceConfigHandler extends ExtRouterServiceConfigHandler {
		public function config($instance) {
			parent::config($instance);
			$instance->registerRouter($this->container->create(SimpleHttpRouter::class, [['Edde\\App']], __METHOD__));
			/** @var $restRouter RestRouter */
			$instance->registerRouter($restRouter = $this->container->create(RestRouter::class, [], __METHOD__));
			$restRouter->registerServiceList([
				$this->container->create(UserService::class),
			]);
		}
	}

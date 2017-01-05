<?php
	declare(strict_types = 1);

	namespace Edde\Ext\Router;

	use Edde\Api\Container\LazyContainerTrait;
	use Edde\Api\Router\IRouterService;
	use Edde\Common\Container\AbstractConfigHandler;

	class RouterServiceConfigHandler extends AbstractConfigHandler {
		use LazyContainerTrait;

		/**
		 * @param IRouterService $instance
		 */
		public function config($instance) {
			$instance->registerRouter($this->container->create(EddeRouter::class, [], __METHOD__));
			$instance->registerRouter($this->container->create(RestRouter::class, [], __METHOD__));
			$instance->registerRouter($this->container->create(HttpRouter::class, [], __METHOD__));
			$instance->registerRouter($this->container->create(SimpleHttpRouter::class, [], __METHOD__));
		}
	}

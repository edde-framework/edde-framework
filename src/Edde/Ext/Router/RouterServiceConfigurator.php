<?php
	declare(strict_types=1);

	namespace Edde\Ext\Router;

	use Edde\Api\Container\LazyContainerTrait;
	use Edde\Api\Router\IRouterService;
	use Edde\Common\Config\AbstractConfigurator;

	class RouterServiceConfigurator extends AbstractConfigurator {
		use LazyContainerTrait;

		/**
		 * @param IRouterService $instance
		 */
		public function config($instance) {
//			$instance->registerRouter(EddeRouter::class);
			$instance->registerRouter(RestRouter::class);
			$instance->registerRouter(HttpRouter::class);
		}
	}

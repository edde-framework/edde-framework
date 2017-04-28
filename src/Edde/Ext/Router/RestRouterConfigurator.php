<?php
	declare(strict_types=1);

	namespace Edde\Ext\Router;

	use Edde\Api\Container\LazyContainerTrait;
	use Edde\Common\Config\AbstractConfigurator;
	use Edde\Ext\Rest\ProtocolService;

	class RestRouterConfigurator extends AbstractConfigurator {
		use LazyContainerTrait;

		/**
		 * @param RestRouter $instance
		 */
		public function config($instance) {
			$instance->registerService($this->container->create(ProtocolService::class));
		}
	}

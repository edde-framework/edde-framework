<?php
	declare(strict_types=1);

	namespace Edde\Ext\Protocol;

	use Edde\Api\Container\LazyContainerTrait;
	use Edde\Api\Protocol\Event\IEventBus;
	use Edde\Api\Protocol\IProtocolService;
	use Edde\Common\Config\AbstractConfigurator;

	class ProtocolServiceConfigurator extends AbstractConfigurator {
		use LazyContainerTrait;

		/**
		 * @param IProtocolService $instance
		 */
		public function config($instance) {
			$instance->registerProtocolHandler($this->container->create(IEventBus::class));
		}
	}

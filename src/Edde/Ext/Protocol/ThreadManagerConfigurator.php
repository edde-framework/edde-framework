<?php
	declare(strict_types=1);

	namespace Edde\Ext\Protocol;

	use Edde\Api\Container\LazyContainerTrait;
	use Edde\Api\Thread\IThreadManager;
	use Edde\Common\Config\AbstractConfigurator;

	class ThreadManagerConfigurator extends AbstractConfigurator {
		use LazyContainerTrait;

		/**
		 * @param IThreadManager $instance
		 */
		public function configure($instance) {
			parent::configure($instance);
			$instance->registerThreadHandler($this->container->create(ProtocolThreadHandler::class));
		}
	}

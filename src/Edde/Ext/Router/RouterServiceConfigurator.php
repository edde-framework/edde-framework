<?php
	declare(strict_types=1);

	namespace Edde\Ext\Router;

	use Edde\Api\Container\Container;
	use Edde\Api\Router\IRouterService;
	use Edde\Common\Config\AbstractConfigurator;
	use Edde\Common\Router\ProtocolRouter;

	class RouterServiceConfigurator extends AbstractConfigurator {
		use Container;

		/**
		 * @param IRouterService $instance
		 */
		public function configure($instance) {
			parent::configure($instance);
			$instance->registerRouter($this->container->create(ProtocolRouter::class, [], __METHOD__));
		}
	}

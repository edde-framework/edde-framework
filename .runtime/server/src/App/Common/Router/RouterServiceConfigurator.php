<?php
	declare(strict_types=1);
	namespace App\Common\Router;

	use Edde\Api\Container\Exception\ContainerException;
	use Edde\Api\Container\Exception\FactoryException;
	use Edde\Api\Router\IRouterService;
	use Edde\Common\Request\Message;
	use Edde\Common\Router\StaticRouter;
	use Edde\Ext\Router\RouterServiceConfigurator as EddeRouterServiceConfigurator;

	class RouterServiceConfigurator extends EddeRouterServiceConfigurator {
		/**
		 * @param IRouterService $instance
		 *
		 * @throws ContainerException
		 * @throws FactoryException
		 */
		public function configure($instance) {
			parent::configure($instance);
			/**
			 * last router is considered as a default
			 */
			$instance->registerRouter($this->container->create(StaticRouter::class, [new Message('index.index-view/action-index')], __METHOD__));
		}
	}

<?php
	declare(strict_types=1);

	namespace Edde\App\Router;

	use Edde\Api\Container\Container;
	use Edde\Api\Router\IRouterService;
	use Edde\Common\Request\Message;
	use Edde\Common\Router\StaticRouter;
	use Edde\Ext\Router\RouterServiceConfigurator as EddeRouterServiceConfigurator;

	class RouterServiceConfigurator extends EddeRouterServiceConfigurator {
		use Container;

		/**
		 * @param IRouterService $instance
		 */
		public function configure($instance) {
			parent::configure($instance);
			$instance->registerDefaultRouter($this->container->create(StaticRouter::class, [new Message('index.index-view/action-index')], __METHOD__));
		}
	}

<?php
	declare(strict_types=1);

	namespace Edde\App\Router;

	use Edde\Api\Container\LazyContainerTrait;
	use Edde\Api\Router\IRouterService;
	use Edde\Common\Protocol\Request\Message;
	use Edde\Common\Router\StaticRouter;

	class RouterServiceConfigurator extends \Edde\Ext\Router\RouterServiceConfigurator {
		use LazyContainerTrait;

		/**
		 * @param IRouterService $instance
		 */
		public function configure($instance) {
			parent::configure($instance);
			$instance->registerDefaultRouter($this->container->create(StaticRouter::class, [new Message('index.index-view/action-index')], __METHOD__));
		}
	}

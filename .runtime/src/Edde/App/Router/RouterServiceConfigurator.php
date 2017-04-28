<?php
	declare(strict_types=1);

	namespace Edde\App\Router;

	use Edde\Api\Container\LazyContainerTrait;
	use Edde\Api\Router\IRouterService;
	use Edde\App\Index\IndexView;
	use Edde\Common\Application\HttpResponseHandler;
	use Edde\Common\Application\Request;
	use Edde\Ext\Router\SimpleHttpRouter;

	class RouterServiceConfigurator extends \Edde\Ext\Router\RouterServiceConfigurator {
		use LazyContainerTrait;

		/**
		 * @param IRouterService $instance
		 */
		public function config($instance) {
			parent::config($instance);
			$instance->setDefaultRequest($this->container->create(Request::class, [
				IndexView::class,
				'actionIndex',
			], __METHOD__), $this->container->create(HttpResponseHandler::class, [], __METHOD__));
			$instance->registerRouter(SimpleHttpRouter::class, [['Edde\\App']]);
		}
	}

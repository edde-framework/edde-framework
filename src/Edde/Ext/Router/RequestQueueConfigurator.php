<?php
	declare(strict_types=1);

	namespace Edde\Ext\Router;

	use Edde\Api\Application\IRequestQueue;
	use Edde\Api\Router\LazyRouterServiceTrait;
	use Edde\Common\Config\AbstractConfigurator;

	class RequestQueueConfigurator extends AbstractConfigurator {
		use LazyRouterServiceTrait;

		/**
		 * @param IRequestQueue $instance
		 */
		public function config($instance) {
			$this->routerService->setup();
			$instance->queue($this->routerService->createRequest());
		}
	}

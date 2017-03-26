<?php
	declare(strict_types=1);

	namespace Edde\Ext\Router;

	use Edde\Api\Application\IRequestQueue;
	use Edde\Api\Application\LazyRequestTrait;
	use Edde\Common\Config\AbstractConfigurator;

	class RequestQueueConfigurator extends AbstractConfigurator {
		use LazyRequestTrait;

		/**
		 * @param IRequestQueue $instance
		 */
		public function config($instance) {
			$instance->queue($this->request);
		}
	}

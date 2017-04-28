<?php
	declare(strict_types=1);

	namespace Edde\Ext\Protocol;

	use Edde\Api\Container\LazyContainerTrait;
	use Edde\Api\Protocol\Request\IRequestService;
	use Edde\Common\Config\AbstractConfigurator;
	use Edde\Common\Protocol\Request\ContainerRequestHandler;

	class RequestServiceConfigurator extends AbstractConfigurator {
		use LazyContainerTrait;

		/**
		 * @param IRequestService $instance
		 */
		public function config($instance) {
			$instance->registerRequestHandler($this->container->create(ContainerRequestHandler::class));
		}
	}

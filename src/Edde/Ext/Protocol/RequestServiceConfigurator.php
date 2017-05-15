<?php
	declare(strict_types=1);

	namespace Edde\Ext\Protocol;

	use Edde\Api\Container\LazyContainerTrait;
	use Edde\Api\Protocol\Request\IRequestService;
	use Edde\Common\Config\AbstractConfigurator;
	use Edde\Ext\Protocol\Request\ClassRequestHandler;
	use Edde\Ext\Protocol\Request\ContainerRequestHandler;

	class RequestServiceConfigurator extends AbstractConfigurator {
		use LazyContainerTrait;

		/**
		 * @param IRequestService $instance
		 */
		public function configure($instance) {
			parent::configure($instance);
			$instance->registerRequestHandler($this->container->create(ClassRequestHandler::class));
			$instance->registerRequestHandler($this->container->create(ContainerRequestHandler::class));
		}
	}

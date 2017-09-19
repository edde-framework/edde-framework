<?php
	declare(strict_types=1);

	namespace Edde\Ext\Protocol;

	use Edde\Api\Container\Container;
	use Edde\Api\Request\IRequestService;
	use Edde\Common\Config\AbstractConfigurator;
	use Edde\Ext\Protocol\Request\ClassRequestHandler;
	use Edde\Ext\Protocol\Request\ContainerRequestHandler;
	use Edde\Ext\Protocol\Request\ControlRequestHandler;
	use Edde\Ext\Protocol\Request\InstanceRequestHandler;

	class RequestServiceConfigurator extends AbstractConfigurator {
		use Container;

		/**
		 * @param IRequestService $instance
		 */
		public function configure($instance) {
			parent::configure($instance);
			$instance->registerRequestHandler($this->container->create(ClassRequestHandler::class, [], __METHOD__));
			$instance->registerRequestHandler($this->container->create(ContainerRequestHandler::class, [], __METHOD__));
			$instance->registerRequestHandler($this->container->create(ControlRequestHandler::class, [], __METHOD__));
			$instance->registerRequestHandler($this->container->create(InstanceRequestHandler::class, [], __METHOD__));
		}
	}

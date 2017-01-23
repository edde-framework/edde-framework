<?php
	declare(strict_types=1);

	namespace Edde\Ext\Router;

	use Edde\Api\Container\LazyContainerTrait;
	use Edde\Common\Config\AbstractConfigurator;

	class RestRouterConfigurator extends AbstractConfigurator {
		use LazyContainerTrait;

		/**
		 * @param RestRouter $instance
		 */
		public function config($instance) {
		}
	}

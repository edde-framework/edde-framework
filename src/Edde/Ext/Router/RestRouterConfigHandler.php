<?php
	declare(strict_types=1);

	namespace Edde\Ext\Router;

	use Edde\Api\Container\LazyContainerTrait;
	use Edde\Common\Container\AbstractConfigHandler;

	class RestRouterConfigHandler extends AbstractConfigHandler {
		use LazyContainerTrait;

		/**
		 * @param RestRouter $instance
		 */
		public function config($instance) {
		}
	}

<?php
	declare(strict_types=1);

	namespace Edde\App\Router;

	use Edde\Api\Container\LazyContainerTrait;
	use Edde\Api\Router\IRouterService;

	class RouterServiceConfigurator extends \Edde\Ext\Router\RouterServiceConfigurator {
		use LazyContainerTrait;

		/**
		 * @param IRouterService $instance
		 */
		public function configure($instance) {
			parent::configure($instance);
		}
	}

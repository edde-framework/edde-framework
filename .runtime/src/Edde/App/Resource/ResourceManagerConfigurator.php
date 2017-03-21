<?php
	declare(strict_types=1);

	namespace Edde\App\Resource;

	use Edde\Api\Container\LazyContainerTrait;
	use Edde\Api\Resource\IResourceManager;
	use Edde\Common\Config\AbstractConfigurator;
	use Edde\Ext\Control\ControlTemplateResourceProvider;

	class ResourceManagerConfigurator extends AbstractConfigurator {
		use LazyContainerTrait;

		/**
		 * @param IResourceManager $instance
		 */
		public function config($instance) {
			$instance->registerResourceProvider($this->container->create(ControlTemplateResourceProvider::class, [], static::class));
		}
	}

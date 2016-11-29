<?php
	declare(strict_types = 1);

	namespace Edde\Module;

	use Edde\Api\Cache\ICacheDirectory;
	use Edde\Api\Cache\ICacheManager;
	use Edde\Api\Cache\ICacheStorage;
	use Edde\Api\Container\IContainer;
	use Edde\Api\Container\IFactoryManager;
	use Edde\Api\File\IRootDirectory;
	use Edde\Api\File\ITempDirectory;
	use Edde\Common\Cache\CacheDirectory;
	use Edde\Common\Cache\CacheManager;
	use Edde\Common\Container\Container;
	use Edde\Common\Container\FactoryManager;
	use Edde\Common\File\TempDirectory;
	use Edde\Common\Runtime\AbstractModule;
	use Edde\Common\Runtime\Event\SetupEvent;
	use Edde\Ext\Cache\FlatFileCacheStorage;
	use Edde\Framework;

	class ContainerModule extends AbstractModule {
		public function setupContainerModule(SetupEvent $setupEvent) {
			$runtime = $setupEvent->getRuntime();
			$runtime->registerFactoryList([
				new Framework(),

				/**
				 * Core system setup - container, cache and related stuff
				 */
				IContainer::class => Container::class,
				IFactoryManager::class => FactoryManager::class,
				ITempDirectory::class => function (IRootDirectory $rootDirectory) {
					return $rootDirectory->directory('temp', TempDirectory::class);
				},
				ICacheDirectory::class => function (ITempDirectory $tempDirectory) {
					return $tempDirectory->directory('cache', CacheDirectory::class);
				},
				ICacheManager::class => CacheManager::class,
				ICacheStorage::class => FlatFileCacheStorage::class,
			]);
		}
	}

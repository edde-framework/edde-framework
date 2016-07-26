<?php
	namespace Edde\Ext\Runtime;

	use Edde\Api\Application\IApplication;
	use Edde\Api\Cache\ICacheDirectory;
	use Edde\Api\Cache\ICacheFactory;
	use Edde\Api\Container\IContainer;
	use Edde\Api\Control\IControlFactory;
	use Edde\Api\Crypt\ICrypt;
	use Edde\Api\Database\IDriver;
	use Edde\Api\File\IRootDirectory;
	use Edde\Api\File\ITempDirectory;
	use Edde\Api\Http\IHttpRequest;
	use Edde\Api\Http\IHttpRequestFactory;
	use Edde\Api\Http\IHttpResponse;
	use Edde\Api\Resource\IResourceIndex;
	use Edde\Api\Resource\IResourceStorable;
	use Edde\Api\Resource\Scanner\IScanner;
	use Edde\Api\Resource\Storage\IFileStorage;
	use Edde\Api\Resource\Storage\IStorageDirectory;
	use Edde\Api\Router\IRoute;
	use Edde\Api\Router\IRouter;
	use Edde\Api\Router\RouterException;
	use Edde\Api\Runtime\RuntimeException;
	use Edde\Api\Schema\ISchemaManager;
	use Edde\Api\Storage\IStorage;
	use Edde\Api\Upgrade\IUpgradeManager;
	use Edde\Api\Web\IJavaScriptCompiler;
	use Edde\Api\Web\IStyleSheetCompiler;
	use Edde\Common\Application\Application;
	use Edde\Common\Cache\CacheDirectory;
	use Edde\Common\Cache\CacheFactory;
	use Edde\Common\Container\Factory\FactoryFactory;
	use Edde\Common\Control\ControlFactory;
	use Edde\Common\Crypt\Crypt;
	use Edde\Common\Database\DatabaseStorage;
	use Edde\Common\File\TempDirectory;
	use Edde\Common\Http\HttpRequestFactory;
	use Edde\Common\Resource\ResourceIndex;
	use Edde\Common\Resource\ResourceSchema;
	use Edde\Common\Resource\ResourceStorable;
	use Edde\Common\Resource\Storage\FileStorage;
	use Edde\Common\Resource\Storage\StorageDirectory;
	use Edde\Common\Router\RouterList;
	use Edde\Common\Runtime\SetupHandler;
	use Edde\Common\Schema\SchemaManager;
	use Edde\Common\Upgrade\UpgradeManager;
	use Edde\Common\Web\JavaScriptCompiler;
	use Edde\Common\Web\StyleSheetCompiler;
	use Edde\Ext\Cache\InMemoryCacheStorage;
	use Edde\Ext\Resource\Scanner\FilesystemScanner;
	use Edde\Ext\Router\CliRouter;
	use Edde\Ext\Router\SimpleRouter;

	class DefaultSetupHandler extends SetupHandler {
		static public function create(ICacheFactory $cacheFactory = null, array $factoryList = []) {
			$setupHandler = parent::create($cacheFactory ?: new CacheFactory(__DIR__, new InMemoryCacheStorage()));
			$setupHandler->registerFactoryList(array_merge([
				/**
				 * Application and presentation layer
				 */
				IApplication::class => Application::class,
				IControlFactory::class => ControlFactory::class,
				IRoute::class => function (IRouter $router) {
					if (($route = $router->route()) === null) {
						throw new RouterException(sprintf('Cannot find route for current application request.'));
					}
					return $route;
				},
				CliRouter::class => CliRouter::class,
				SimpleRouter::class => SimpleRouter::class,
				IRouter::class => $setupHandler->factory(RouterList::class, function (IContainer $container, RouterList $routerList) {
					$routerList->registerRouter($container->create(CliRouter::class));
					$routerList->registerRouter($container->create(SimpleRouter::class));
				}),
				/**
				 * Http request support
				 */
				IHttpRequestFactory::class => HttpRequestFactory::class,
				IHttpRequest::class => function (IHttpRequestFactory $httpRequestFactory) {
					return $httpRequestFactory->create();
				},
				IHttpResponse::class => function () {
					throw new RuntimeException(sprintf('Do not request [%s] from the global space (container) as it is bad practice.', IHttpResponse::class));
				},
				ISchemaManager::class => $setupHandler->factory(SchemaManager::class, function (SchemaManager $schemaManager) {
					$schemaManager->addSchema(new ResourceSchema());
				}),
				IRootDirectory::class => function () {
					throw new RuntimeException(sprintf('If you want use root directory [%s], you must register it to the container!', IRootDirectory::class));
				},
				ITempDirectory::class => function (IRootDirectory $rootDirectory) {
					return new TempDirectory($rootDirectory->getDirectory() . '/temp');
				},
				ICacheDirectory::class => function (IRootDirectory $rootDirectory) {
					return new CacheDirectory($rootDirectory->getDirectory() . '/temp/cache');
				},
				IStorageDirectory::class => function (IRootDirectory $rootDirectory) {
					return new StorageDirectory($rootDirectory->getDirectory() . '/.storage');
				},
				ICrypt::class => Crypt::class,
				IScanner::class => function (IRootDirectory $rootDirectory) {
					return new FilesystemScanner($rootDirectory);
				},
				IFileStorage::class => FileStorage::class,
				IDriver::class => function () {
					throw new RuntimeException(sprintf('If you want to use DatabaseStorage (or [%s]), you must register it to the container at first!', IDriver::class));
				},
				IStorage::class => DatabaseStorage::class,
				ResourceStorable::class => function (IResourceIndex $resourceIndex) {
					return $resourceIndex->createResourceStorable();
				},
				IResourceIndex::class => ResourceIndex::class,
				IUpgradeManager::class => UpgradeManager::class,
				IResourceStorable::class => FactoryFactory::create(ResourceStorable::class, function (IResourceIndex $resourceIndex) {
					return $resourceIndex->createResourceStorable();
				}, false),
				IStyleSheetCompiler::class => StyleSheetCompiler::class,
				IJavaScriptCompiler::class => JavaScriptCompiler::class,
			], $factoryList));
			return $setupHandler;
		}
	}

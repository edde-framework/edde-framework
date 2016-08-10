<?php
	declare(strict_types = 1);

	namespace Edde\Ext\Runtime;

	use Edde\Api\Application\IApplication;
	use Edde\Api\Cache\ICacheDirectory;
	use Edde\Api\Cache\ICacheFactory;
	use Edde\Api\Cache\ICacheStorage;
	use Edde\Api\Container\IContainer;
	use Edde\Api\Container\IFactory;
	use Edde\Api\Crate\ICrateDirectory;
	use Edde\Api\Crate\ICrateFactory;
	use Edde\Api\Crate\ICrateGenerator;
	use Edde\Api\Crypt\ICryptEngine;
	use Edde\Api\Database\IDriver;
	use Edde\Api\File\IRootDirectory;
	use Edde\Api\File\ITempDirectory;
	use Edde\Api\Http\IHttpRequest;
	use Edde\Api\Http\IHttpRequestFactory;
	use Edde\Api\Http\IHttpResponse;
	use Edde\Api\Resource\IResourceIndex;
	use Edde\Api\Resource\IResourceManager;
	use Edde\Api\Resource\IResourceStorable;
	use Edde\Api\Resource\Scanner\IScanner;
	use Edde\Api\Resource\Storage\IFileStorage;
	use Edde\Api\Resource\Storage\IStorageDirectory;
	use Edde\Api\Router\IRoute;
	use Edde\Api\Router\IRouterService;
	use Edde\Api\Router\RouterException;
	use Edde\Api\Runtime\RuntimeException;
	use Edde\Api\Schema\ISchemaFactory;
	use Edde\Api\Schema\ISchemaManager;
	use Edde\Api\Storage\IStorage;
	use Edde\Api\Template\ITemplate;
	use Edde\Api\Template\ITemplateFactory;
	use Edde\Api\Upgrade\IUpgradeManager;
	use Edde\Api\Web\IJavaScriptCompiler;
	use Edde\Api\Web\IStyleSheetCompiler;
	use Edde\Api\Xml\IXmlParser;
	use Edde\Common\Application\Application;
	use Edde\Common\Cache\CacheDirectory;
	use Edde\Common\Cache\CacheFactory;
	use Edde\Common\Container\Factory\FactoryFactory;
	use Edde\Common\Crate\CrateDirectory;
	use Edde\Common\Crate\CrateFactory;
	use Edde\Common\Crate\CrateGenerator;
	use Edde\Common\Crypt\CryptEngine;
	use Edde\Common\Database\DatabaseStorage;
	use Edde\Common\File\TempDirectory;
	use Edde\Common\Http\HttpRequestFactory;
	use Edde\Common\Resource\ResourceIndex;
	use Edde\Common\Resource\ResourceManager;
	use Edde\Common\Resource\ResourceSchema;
	use Edde\Common\Resource\ResourceStorable;
	use Edde\Common\Resource\Storage\FileStorage;
	use Edde\Common\Resource\Storage\StorageDirectory;
	use Edde\Common\Router\RouterService;
	use Edde\Common\Runtime\SetupHandler;
	use Edde\Common\Schema\SchemaFactory;
	use Edde\Common\Schema\SchemaManager;
	use Edde\Common\Template\TemplateFactory;
	use Edde\Common\Upgrade\UpgradeManager;
	use Edde\Common\Web\JavaScriptCompiler;
	use Edde\Common\Web\StyleSheetCompiler;
	use Edde\Common\Xml\XmlParser;
	use Edde\Common\Xml\XmlResourceHandler;
	use Edde\Ext\Cache\InMemoryCacheStorage;
	use Edde\Ext\Database\Sqlite\SqliteDriver;
	use Edde\Ext\Resource\JsonResourceHandler;
	use Edde\Ext\Resource\Scanner\FilesystemScanner;
	use Edde\Ext\Router\CliRouter;
	use Edde\Ext\Router\SimpleRouter;
	use Edde\Ext\Upgrade\InitialStorageUpgrade;

	class DefaultSetupHandler extends SetupHandler {
		static public function create(ICacheFactory $cacheFactory = null, array $factoryList = []) {
			$setupHandler = parent::create($cacheFactory ?: new CacheFactory(__DIR__, new InMemoryCacheStorage()));
			$setupHandler->registerFactoryList(array_merge([
				ICacheStorage::class => InMemoryCacheStorage::class,
				/**
				 * Application and presentation layer
				 */
				IApplication::class => Application::class,
				IRouterService::class => RouterService::class,
				IRoute::class => function (IRouterService $routerService) {
					if (($route = $routerService->route()) === null) {
						throw new RouterException(sprintf('Cannot find route for current application request.'));
					}
					return $route;
				},
				CliRouter::class,
				SimpleRouter::class,
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
				ISchemaFactory::class => SchemaFactory::class,
				ISchemaManager::class => SchemaManager::class,
				IRootDirectory::class => function () {
					throw new RuntimeException(sprintf('If you want use root directory [%s], you must register it to the container!', IRootDirectory::class));
				},
				ITempDirectory::class => function (IRootDirectory $rootDirectory) {
					return new TempDirectory($rootDirectory->getDirectory() . '/temp');
				},
				ICacheDirectory::class => function (ITempDirectory $tempDirectory) {
					return new CacheDirectory($tempDirectory->getDirectory() . '/cache');
				},
				IStorageDirectory::class => function (IRootDirectory $rootDirectory) {
					return new StorageDirectory($rootDirectory->getDirectory() . '/.storage');
				},
				ICryptEngine::class => CryptEngine::class,
				IScanner::class => function (IRootDirectory $rootDirectory) {
					return new FilesystemScanner($rootDirectory);
				},
				IFileStorage::class => FileStorage::class,
				IDriver::class => function (IStorageDirectory $storageDirectory) {
					return new SqliteDriver('sqlite:' . $storageDirectory->filename('storage.sqlite'));
				},
				ICrateGenerator::class => CrateGenerator::class,
				ICrateFactory::class => CrateFactory::class,
				ICrateDirectory::class => function (IStorageDirectory $storageDirectory) {
					return new CrateDirectory($storageDirectory->getDirectory() . '/crate');
				},
				IStorage::class => DatabaseStorage::class,
				IResourceIndex::class => ResourceIndex::class,
				IResourceManager::class => ResourceManager::class,
				IUpgradeManager::class => UpgradeManager::class,
				IResourceStorable::class => FactoryFactory::create(ResourceStorable::class, function (IResourceIndex $resourceIndex) {
					return $resourceIndex->createResourceStorable();
				}, false),
				ITemplateFactory::class => TemplateFactory::class,
				ITemplate::class => [
					function (ITemplateFactory $templateFactory) {
						return $templateFactory->create();
					},
					function (IFactory $factory) {
						$factory->setSingleton(false);
					},
				],
				IStyleSheetCompiler::class => StyleSheetCompiler::class,
				IJavaScriptCompiler::class => JavaScriptCompiler::class,
				IXmlParser::class => XmlParser::class,
				XmlResourceHandler::class,

				JsonResourceHandler::class,

				InitialStorageUpgrade::class,
			], $factoryList));
			$setupHandler->onSetup(ISchemaManager::class, function (ISchemaManager $schemaManager) {
				$schemaManager->addSchema(new ResourceSchema());
			});
			$setupHandler->onSetup(IRouterService::class, function (IContainer $container, IRouterService $routerService) {
				$routerService->registerRouter($container->create(CliRouter::class));
				$routerService->registerRouter($container->create(SimpleRouter::class));
			});
			$setupHandler->onSetup(IApplication::class, function (ICrateGenerator $crateGenerator, IApplication $application) {
				$crateGenerator->generate();
			});
			$setupHandler->onSetup(IResourceManager::class, function (IContainer $container, IResourceManager $resourceManager) {
				$resourceManager->registerResourceHandler($container->create(XmlResourceHandler::class));
				$resourceManager->registerResourceHandler($container->create(JsonResourceHandler::class));
			});
			return $setupHandler;
		}
	}

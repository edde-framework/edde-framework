<?php
	namespace Edde\Ext\Runtime;

	use Edde\Api\Application\IApplication;
	use Edde\Api\Cache\ICacheFactory;
	use Edde\Api\Container\IContainer;
	use Edde\Api\Container\IFactory;
	use Edde\Api\Control\IControlFactory;
	use Edde\Api\Http\IHttpRequest;
	use Edde\Api\Http\IHttpRequestFactory;
	use Edde\Api\Http\IHttpResponse;
	use Edde\Api\Resource\IResourceManager;
	use Edde\Api\Router\IRoute;
	use Edde\Api\Router\IRouter;
	use Edde\Api\Router\RouterException;
	use Edde\Api\Runtime\ISetupHandler;
	use Edde\Api\Runtime\RuntimeException;
	use Edde\Api\Schema\ISchemaManager;
	use Edde\Api\Upgrade\IUpgradeManager;
	use Edde\Common\Application\Application;
	use Edde\Common\Cache\CacheFactory;
	use Edde\Common\Container\Factory\FactoryFactory;
	use Edde\Common\Control\ControlFactory;
	use Edde\Common\Http\HttpRequestFactory;
	use Edde\Common\Resource\ResourceManager;
	use Edde\Common\Resource\ResourceSchema;
	use Edde\Common\Resource\ResourceStorable;
	use Edde\Common\Router\RouterList;
	use Edde\Common\Runtime\SetupHandler;
	use Edde\Common\Schema\SchemaManager;
	use Edde\Common\Upgrade\UpgradeManager;
	use Edde\Ext\Cache\InMemoryCacheStorage;
	use Edde\Ext\Link\PublicLinkGenerator;
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
				IRouter::class => [
					RouterList::class,
					function (IFactory $factory) {
						$factory->onSetup(function (IContainer $container, RouterList $routerList) {
							$routerList->onSetup(function (RouterList $routerList) use ($container) {
								$routerList->registerRouter($container->create(CliRouter::class));
								$routerList->registerRouter($container->create(SimpleRouter::class));
							});
						});
					},
				],
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
				PublicLinkGenerator::class => function () {
					throw new RuntimeException(sprintf('[%s] needs setup; please use current [%s] and register your own [%s] factory.', PublicLinkGenerator::class, ISetupHandler::class, PublicLinkGenerator::class));
				},
				ISchemaManager::class => [
					SchemaManager::class,
					function (IFactory $factory) {
						$factory->onSetup(function (SchemaManager $schemaManager) {
							$schemaManager->onSetup(function (SchemaManager $schemaManager, ResourceSchema $resourceSchema) {
								$schemaManager->addSchema($resourceSchema);
							});
						});
					},
				],
				IResourceManager::class => ResourceManager::class,
				IUpgradeManager::class => UpgradeManager::class,
				ResourceStorable::class => FactoryFactory::create(ResourceStorable::class, function (IResourceManager $resourceManager) {
					return $resourceManager->createResourceStorable();
				}, false),
			], $factoryList));
			return $setupHandler;
		}
	}

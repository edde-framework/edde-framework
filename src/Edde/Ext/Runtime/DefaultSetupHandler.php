<?php
	declare(strict_types = 1);

	namespace Edde\Ext\Runtime;

	use Edde\Api\Application\IApplication;
	use Edde\Api\Application\IErrorControl;
	use Edde\Api\Application\IRequest;
	use Edde\Api\Application\IResponseManager;
	use Edde\Api\Asset\IAssetDirectory;
	use Edde\Api\Asset\IAssetStorage;
	use Edde\Api\Asset\IStorageDirectory;
	use Edde\Api\Cache\ICacheDirectory;
	use Edde\Api\Cache\ICacheFactory;
	use Edde\Api\Cache\ICacheStorage;
	use Edde\Api\Client\IHttpClient;
	use Edde\Api\Container\IContainer;
	use Edde\Api\Converter\IConverterManager;
	use Edde\Api\Crate\ICrateDirectory;
	use Edde\Api\Crate\ICrateFactory;
	use Edde\Api\Crate\ICrateGenerator;
	use Edde\Api\Crypt\ICryptEngine;
	use Edde\Api\Database\IDriver;
	use Edde\Api\Event\IEventBus;
	use Edde\Api\File\IRootDirectory;
	use Edde\Api\File\ITempDirectory;
	use Edde\Api\Html\ITemplateDirectory;
	use Edde\Api\Http\IBody;
	use Edde\Api\Http\ICookieList;
	use Edde\Api\Http\IHeaderList;
	use Edde\Api\Http\IHostUrl;
	use Edde\Api\Http\IHttpRequest;
	use Edde\Api\Http\IHttpRequestFactory;
	use Edde\Api\Http\IHttpResponse;
	use Edde\Api\Http\IPostList;
	use Edde\Api\Http\IRequestUrl;
	use Edde\Api\IAssetsDirectory;
	use Edde\Api\Identity\IAuthenticatorManager;
	use Edde\Api\Identity\IIdentity;
	use Edde\Api\Identity\IIdentityManager;
	use Edde\Api\IEddeDirectory;
	use Edde\Api\Link\ILinkFactory;
	use Edde\Api\Resource\IResourceManager;
	use Edde\Api\Router\IRouterService;
	use Edde\Api\Runtime\ISetupHandler;
	use Edde\Api\Runtime\RuntimeException;
	use Edde\Api\Schema\ISchemaFactory;
	use Edde\Api\Schema\ISchemaManager;
	use Edde\Api\Session\IFingerprint;
	use Edde\Api\Session\ISessionManager;
	use Edde\Api\Storage\IStorage;
	use Edde\Api\Template\IHelperSet;
	use Edde\Api\Template\IMacroSet;
	use Edde\Api\Template\ITemplateManager;
	use Edde\Api\Upgrade\IUpgradeManager;
	use Edde\Api\Web\IJavaScriptCompiler;
	use Edde\Api\Web\IStyleSheetCompiler;
	use Edde\Api\Xml\IXmlParser;
	use Edde\Common\Application\Application;
	use Edde\Common\Application\ResponseManager;
	use Edde\Common\Asset\AssetDirectory;
	use Edde\Common\Asset\AssetStorage;
	use Edde\Common\Asset\StorageDirectory;
	use Edde\Common\AssetsDirectory;
	use Edde\Common\Cache\CacheDirectory;
	use Edde\Common\Cache\CacheFactory;
	use Edde\Common\Client\HttpClient;
	use Edde\Common\Converter\ConverterManager;
	use Edde\Common\Crate\CrateDirectory;
	use Edde\Common\Crate\CrateFactory;
	use Edde\Common\Crate\CrateGenerator;
	use Edde\Common\Crypt\CryptEngine;
	use Edde\Common\Database\DatabaseStorage;
	use Edde\Common\EddeDirectory;
	use Edde\Common\Event\EventBus;
	use Edde\Common\File\TempDirectory;
	use Edde\Common\Html\Converter\HtmlConverter;
	use Edde\Common\Html\TemplateDirectory;
	use Edde\Common\Http\HostUrl;
	use Edde\Common\Http\HttpRequestFactory;
	use Edde\Common\Http\HttpResponse;
	use Edde\Common\Identity\AuthenticatorManager;
	use Edde\Common\Identity\IdentityManager;
	use Edde\Common\Link\LinkFactory;
	use Edde\Common\Resource\ResourceManager;
	use Edde\Common\Router\RouterService;
	use Edde\Common\Runtime\SetupHandler;
	use Edde\Common\Schema\SchemaFactory;
	use Edde\Common\Schema\SchemaManager;
	use Edde\Common\Session\DummyFingerprint;
	use Edde\Common\Session\SessionManager;
	use Edde\Common\Template\TemplateManager;
	use Edde\Common\Upgrade\UpgradeManager;
	use Edde\Common\Web\JavaScriptCompiler;
	use Edde\Common\Web\StyleSheetCompiler;
	use Edde\Common\Xml\XmlParser;
	use Edde\Ext\Application\ExceptionErrorControl;
	use Edde\Ext\Cache\InMemoryCacheStorage;
	use Edde\Ext\Converter\ArrayConverter;
	use Edde\Ext\Converter\HttpConverter;
	use Edde\Ext\Converter\JsonConverter;
	use Edde\Ext\Converter\PhpConverter;
	use Edde\Ext\Converter\RedirectConverter;
	use Edde\Ext\Converter\XmlConverter;
	use Edde\Ext\Database\Sqlite\SqliteDriver;
	use Edde\Ext\Router\SimpleRouter;
	use Edde\Ext\Template\DefaultMacroSet;
	use Edde\Framework;

	/**
	 * Default application configuration; this shold be base for all setups.
	 */
	class DefaultSetupHandler extends SetupHandler {
		/**
		 * @inheritdoc
		 * @throws RuntimeException
		 */
		static public function create(ICacheFactory $cacheFactory = null, array $factoryList = []): ISetupHandler {
			return parent::create($cacheFactory ?: new CacheFactory(__DIR__, new InMemoryCacheStorage()))
				->registerFactoryList(array_merge([
					Framework::class,
					IEddeDirectory::class => function () {
						/**
						 * this is darkes possible magic; don't use this constant, don't use this interface!
						 *
						 * It's here only for internal use.
						 */
						return new EddeDirectory(EDDE_ROOT_DIRECTORY);
					},
					IAssetsDirectory::class => function (IEddeDirectory $eddeDirectory) {
						return $eddeDirectory->directory('assets', AssetsDirectory::class);
					},
					ICacheStorage::class => InMemoryCacheStorage::class,
					/**
					 * Application and presentation layer
					 */
					IApplication::class => Application::class,
					IErrorControl::class => ExceptionErrorControl::class,
					IRouterService::class => RouterService::class,
					IRequest::class => function (IRouterService $routerService) {
						return $routerService->createRequest();
					},
					IResponseManager::class => ResponseManager::class,
					/**
					 * Http request support
					 */
					IHttpRequestFactory::class => HttpRequestFactory::class,
					IHttpRequest::class => function (IHttpRequestFactory $httpRequestFactory) {
						return $httpRequestFactory->create();
					},
					IRequestUrl::class => function (IHttpRequest $httpRequest) {
						return $httpRequest->getRequestUrl();
					},
					IHeaderList::class => function (IHttpRequest $httpRequest) {
						return $httpRequest->getHeaderList();
					},
					ICookieList::class => function (IHttpRequest $httpRequest) {
						return $httpRequest->getCookieList();
					},
					IPostList::class => function (IHttpRequest $httpRequest) {
						return $httpRequest->getPostList();
					},
					IBody::class => function (IHttpRequest $httpRequest) {
						return $httpRequest->getBody();
					},
					IHttpResponse::class => HttpResponse::class,
					IHttpClient::class => HttpClient::class,
					ISessionManager::class => SessionManager::class,
					IFingerprint::class => DummyFingerprint::class,
					ISchemaFactory::class => SchemaFactory::class,
					ISchemaManager::class => SchemaManager::class,
					IRootDirectory::class => function () {
						throw new RuntimeException(sprintf('If you want use root directory [%s], you must rregister it to the container!', IRootDirectory::class));
					},
					IAssetDirectory::class => function (IRootDirectory $rootDirectory) {
						return $rootDirectory->directory('.assets', AssetDirectory::class);
					},
					ITempDirectory::class => function (IRootDirectory $rootDirectory) {
						return $rootDirectory->directory('temp', TempDirectory::class);
					},
					ICacheDirectory::class => function (ITempDirectory $tempDirectory) {
						return $tempDirectory->directory('cache', CacheDirectory::class);
					},
					IStorageDirectory::class => function (IAssetDirectory $assetDirectory) {
						return $assetDirectory->directory('storage', StorageDirectory::class);
					},
					ITemplateDirectory::class => function (IAssetDirectory $assetDirectory) {
						return $assetDirectory->directory('template', TemplateDirectory::class);
					},
					ICryptEngine::class => CryptEngine::class,
					IAssetStorage::class => AssetStorage::class,
					IDriver::class => function (IAssetDirectory $assetDirectory) {
						return new SqliteDriver('sqlite:' . $assetDirectory->filename('storage.sqlite'));
					},
					ICrateGenerator::class => CrateGenerator::class,
					ICrateFactory::class => CrateFactory::class,
					ICrateDirectory::class => function (IAssetDirectory $assetDirectory) {
						return $assetDirectory->directory('crate', CrateDirectory::class);
					},
					IStorage::class => DatabaseStorage::class,
					IResourceManager::class => ResourceManager::class,
					IConverterManager::class => ConverterManager::class,
					IUpgradeManager::class => UpgradeManager::class,
					ITemplateManager::class => TemplateManager::class,
					IMacroSet::class => function (IContainer $container) {
						return DefaultMacroSet::macroSet($container);
					},
					IHelperSet::class => function (IContainer $container) {
						return DefaultMacroSet::helperSet($container);
					},
					IStyleSheetCompiler::class => StyleSheetCompiler::class,
					IJavaScriptCompiler::class => JavaScriptCompiler::class,
					IXmlParser::class => XmlParser::class,
					IHostUrl::class => function (IHttpRequest $httpRequest) {
						return HostUrl::create((string)$httpRequest->getRequestUrl());
					},
					ILinkFactory::class => LinkFactory::class,
					IAuthenticatorManager::class => AuthenticatorManager::class,
					IIdentityManager::class => IdentityManager::class,
					IIdentity::class => function (IIdentityManager $identityManager) {
						return $identityManager->identity();
					},
					IEventBus::class => EventBus::class,
				], $factoryList))
				->deffered(IRouterService::class, function (IContainer $container, IRouterService $routerService) {
					$routerService->registerRouter($container->create(SimpleRouter::class));
				})
				->deffered(IConverterManager::class, function (IContainer $container, IConverterManager $converterManager) {
					$converterManager->registerConverter($container->create(XmlConverter::class));
					$converterManager->registerConverter($container->create(ArrayConverter::class));
					$converterManager->registerConverter($container->create(JsonConverter::class));
					$converterManager->registerConverter($container->create(PhpConverter::class));

					$converterManager->registerConverter($container->create(HtmlConverter::class));
					$converterManager->registerConverter($container->create(HttpConverter::class));
					$converterManager->registerConverter($container->create(RedirectConverter::class));
				});
		}
	}

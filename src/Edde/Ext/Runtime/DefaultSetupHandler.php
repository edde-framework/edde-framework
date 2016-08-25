<?php
	declare(strict_types = 1);

	namespace Edde\Ext\Runtime;

	use Edde\Api\Application\IApplication;
	use Edde\Api\Application\IErrorControl;
	use Edde\Api\Cache\ICacheDirectory;
	use Edde\Api\Cache\ICacheFactory;
	use Edde\Api\Cache\ICacheStorage;
	use Edde\Api\Container\IContainer;
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
	use Edde\Api\Identity\IIdentity;
	use Edde\Api\Identity\IIdentityManager;
	use Edde\Api\Link\IHostUrl;
	use Edde\Api\Link\ILinkFactory;
	use Edde\Api\Resource\IResourceManager;
	use Edde\Api\Resource\Storage\IFileStorage;
	use Edde\Api\Resource\Storage\IStorageDirectory;
	use Edde\Api\Router\IRoute;
	use Edde\Api\Router\IRouterService;
	use Edde\Api\Runtime\RuntimeException;
	use Edde\Api\Schema\ISchemaFactory;
	use Edde\Api\Schema\ISchemaManager;
	use Edde\Api\Session\IFingerprint;
	use Edde\Api\Session\ISessionManager;
	use Edde\Api\Storage\IStorage;
	use Edde\Api\Template\ITemplateDirectory;
	use Edde\Api\Template\ITemplateManager;
	use Edde\Api\Upgrade\IUpgradeManager;
	use Edde\Api\Web\IJavaScriptCompiler;
	use Edde\Api\Web\IStyleSheetCompiler;
	use Edde\Api\Xml\IXmlParser;
	use Edde\Common\Application\Application;
	use Edde\Common\Cache\CacheDirectory;
	use Edde\Common\Cache\CacheFactory;
	use Edde\Common\Crate\CrateDirectory;
	use Edde\Common\Crate\CrateFactory;
	use Edde\Common\Crate\CrateGenerator;
	use Edde\Common\Crypt\CryptEngine;
	use Edde\Common\Database\DatabaseStorage;
	use Edde\Common\File\TempDirectory;
	use Edde\Common\Html\DivControl;
	use Edde\Common\Html\SpanControl;
	use Edde\Common\Html\Value\PasswordInputControl;
	use Edde\Common\Html\Value\TextInputControl;
	use Edde\Common\Http\HttpRequestFactory;
	use Edde\Common\Identity\Identity;
	use Edde\Common\Identity\IdentityManager;
	use Edde\Common\Link\HostUrl;
	use Edde\Common\Link\LinkFactory;
	use Edde\Common\Resource\ResourceManager;
	use Edde\Common\Resource\Storage\FileStorage;
	use Edde\Common\Resource\Storage\StorageDirectory;
	use Edde\Common\Router\RouterService;
	use Edde\Common\Runtime\SetupHandler;
	use Edde\Common\Schema\SchemaFactory;
	use Edde\Common\Schema\SchemaManager;
	use Edde\Common\Session\DummyFingerprint;
	use Edde\Common\Session\SessionManager;
	use Edde\Common\Template\Macro\Control\BindIdAttributeMacro;
	use Edde\Common\Template\Macro\Control\ButtonMacro;
	use Edde\Common\Template\Macro\Control\ControlMacro;
	use Edde\Common\Template\Macro\Control\CssMacro;
	use Edde\Common\Template\Macro\Control\HeaderMacro;
	use Edde\Common\Template\Macro\Control\JsMacro;
	use Edde\Common\Template\Macro\Control\PassMacro;
	use Edde\Common\Template\Macro\Control\SchemaMacro;
	use Edde\Common\Template\Macro\Control\TemplateMacro;
	use Edde\Common\Template\Macro\IncludeMacro;
	use Edde\Common\Template\Macro\LoopMacro;
	use Edde\Common\Template\Macro\SwitchMacro;
	use Edde\Common\Template\TemplateDirectory;
	use Edde\Common\Template\TemplateManager;
	use Edde\Common\Upgrade\UpgradeManager;
	use Edde\Common\Web\JavaScriptCompiler;
	use Edde\Common\Web\StyleSheetCompiler;
	use Edde\Common\Xml\XmlParser;
	use Edde\Common\Xml\XmlResourceHandler;
	use Edde\Ext\Application\ExceptionErrorControl;
	use Edde\Ext\Cache\InMemoryCacheStorage;
	use Edde\Ext\Database\Sqlite\SqliteDriver;
	use Edde\Ext\Resource\JsonResourceHandler;
	use Edde\Ext\Resource\PhpResourceHandler;
	use Edde\Ext\Router\SimpleRouter;

	class DefaultSetupHandler extends SetupHandler {
		static public function create(ICacheFactory $cacheFactory = null, array $factoryList = []) {
			return parent::create($cacheFactory ?: new CacheFactory(__DIR__, new InMemoryCacheStorage()))
				->registerFactoryList(array_merge([
					ICacheStorage::class => InMemoryCacheStorage::class,
					/**
					 * Application and presentation layer
					 */
					IApplication::class => Application::class,
					IErrorControl::class => ExceptionErrorControl::class,
					IRouterService::class => RouterService::class,
					IRoute::class => function (IRouterService $routerService) {
						return $routerService->route();
					},
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
					ISessionManager::class => SessionManager::class,
					IFingerprint::class => DummyFingerprint::class,
					IIdentity::class => Identity::class,
					IIdentityManager::class => IdentityManager::class,
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
					ITemplateDirectory::class => functioN (IStorageDirectory $storageDirectory) {
						return new TemplateDirectory($storageDirectory->getDirectory() . '/template');
					},
					ICryptEngine::class => CryptEngine::class,
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
					IResourceManager::class => ResourceManager::class,
					IUpgradeManager::class => UpgradeManager::class,
					ITemplateManager::class => TemplateManager::class,
					IStyleSheetCompiler::class => StyleSheetCompiler::class,
					IJavaScriptCompiler::class => JavaScriptCompiler::class,
					IXmlParser::class => XmlParser::class,
					IHostUrl::class => function (IHttpRequest $httpRequest) {
						return HostUrl::create((string)$httpRequest->getUrl());
					},
					ILinkFactory::class => LinkFactory::class,
				], $factoryList))
				->onSetup(IRouterService::class, function (IContainer $container, IRouterService $routerService) {
//					$routerService->registerRouter($container->create(CliRouter::class));
					$routerService->registerRouter($container->create(SimpleRouter::class));
				})
				->onSetup(IResourceManager::class, function (IContainer $container, IResourceManager $resourceManager) {
					$resourceManager->registerResourceHandler($container->create(XmlResourceHandler::class));
					$resourceManager->registerResourceHandler($container->create(JsonResourceHandler::class));
					$resourceManager->registerResourceHandler($container->create(PhpResourceHandler::class));
				})
				->onSetup(ITemplateManager::class, function (IContainer $container, ITemplateManager $templateManager) {
					$templateManager->registerMacroList([
						new TemplateMacro(),
						new ControlMacro('div', DivControl::class),
						new ControlMacro('span', SpanControl::class),
						new ControlMacro('password', PasswordInputControl::class),
						new ControlMacro('text', TextInputControl::class),
						new CssMacro(),
						new JsMacro(),
						new ButtonMacro(),
						new HeaderMacro(),
						new PassMacro(),
						$container->create(IncludeMacro::class),
						$container->create(SwitchMacro::class),
						$container->create(BindIdAttributeMacro::class),
						$container->create(SchemaMacro::class),
						$container->create(LoopMacro::class),
					]);
				});
		}
	}

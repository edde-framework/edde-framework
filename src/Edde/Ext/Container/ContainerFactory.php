<?php
	declare(strict_types=1);

	namespace Edde\Ext\Container;

	use Edde\Api\Acl\IAcl;
	use Edde\Api\Acl\IAclManager;
	use Edde\Api\Application\IApplication;
	use Edde\Api\Application\IContext;
	use Edde\Api\Application\IRequest;
	use Edde\Api\Application\IRequestQueue;
	use Edde\Api\Application\IResponseManager;
	use Edde\Api\Asset\IAssetDirectory;
	use Edde\Api\Asset\IAssetStorage;
	use Edde\Api\Asset\IStorageDirectory;
	use Edde\Api\Cache\ICache;
	use Edde\Api\Cache\ICacheDirectory;
	use Edde\Api\Cache\ICacheManager;
	use Edde\Api\Cache\ICacheStorage;
	use Edde\Api\Container\ContainerException;
	use Edde\Api\Container\FactoryException;
	use Edde\Api\Container\IContainer;
	use Edde\Api\Container\IFactory;
	use Edde\Api\Converter\IConverterManager;
	use Edde\Api\Crate\ICrate;
	use Edde\Api\Crate\ICrateDirectory;
	use Edde\Api\Crate\ICrateFactory;
	use Edde\Api\Crypt\ICryptEngine;
	use Edde\Api\Database\IDriver;
	use Edde\Api\Database\IDsn;
	use Edde\Api\EddeException;
	use Edde\Api\File\IRootDirectory;
	use Edde\Api\File\ITempDirectory;
	use Edde\Api\Html\IHtmlGenerator;
	use Edde\Api\Http\Client\IHttpClient;
	use Edde\Api\Http\IHostUrl;
	use Edde\Api\Http\IHttpRequest;
	use Edde\Api\Http\IHttpResponse;
	use Edde\Api\Identity\IAuthenticatorManager;
	use Edde\Api\Identity\IIdentity;
	use Edde\Api\Identity\IIdentityManager;
	use Edde\Api\Link\ILinkFactory;
	use Edde\Api\Log\ILogDirectory;
	use Edde\Api\Log\ILogService;
	use Edde\Api\Resource\IResourceManager;
	use Edde\Api\Resource\IResourceProvider;
	use Edde\Api\Router\IRouterService;
	use Edde\Api\Runtime\IRuntime;
	use Edde\Api\Schema\ISchemaManager;
	use Edde\Api\Session\IFingerprint;
	use Edde\Api\Session\ISessionDirectory;
	use Edde\Api\Session\ISessionManager;
	use Edde\Api\Storage\IStorage;
	use Edde\Api\Template\ICompiler;
	use Edde\Api\Template\ITemplate;
	use Edde\Api\Template\ITemplateDirectory;
	use Edde\Api\Template\ITemplateManager;
	use Edde\Api\Translator\ITranslator;
	use Edde\Api\Upgrade\IUpgradeManager;
	use Edde\Api\Web\IJavaScriptCompiler;
	use Edde\Api\Web\IStyleSheetCompiler;
	use Edde\Api\Xml\IXmlParser;
	use Edde\Common\Acl\Acl;
	use Edde\Common\Acl\AclManager;
	use Edde\Common\Application\Application;
	use Edde\Common\Application\RequestQueue;
	use Edde\Common\Application\ResponseManager;
	use Edde\Common\Asset\AssetDirectory;
	use Edde\Common\Asset\AssetStorage;
	use Edde\Common\Asset\StorageDirectory;
	use Edde\Common\Cache\Cache;
	use Edde\Common\Cache\CacheDirectory;
	use Edde\Common\Cache\CacheManager;
	use Edde\Common\Container\Container;
	use Edde\Common\Converter\ConverterManager;
	use Edde\Common\Crate\Crate;
	use Edde\Common\Crate\CrateDirectory;
	use Edde\Common\Crate\CrateFactory;
	use Edde\Common\Crypt\CryptEngine;
	use Edde\Common\Database\DatabaseStorage;
	use Edde\Common\File\TempDirectory;
	use Edde\Common\Html\Html5Generator;
	use Edde\Common\Http\Client\HttpClient;
	use Edde\Common\Http\HttpRequest;
	use Edde\Common\Http\HttpResponse;
	use Edde\Common\Identity\AuthenticatorManager;
	use Edde\Common\Identity\IdentityManager;
	use Edde\Common\Log\LogDirectory;
	use Edde\Common\Log\LogService;
	use Edde\Common\Object;
	use Edde\Common\Resource\ResourceManager;
	use Edde\Common\Router\RouterService;
	use Edde\Common\Runtime\Runtime;
	use Edde\Common\Schema\SchemaManager;
	use Edde\Common\Session\SessionDirectory;
	use Edde\Common\Session\SessionManager;
	use Edde\Common\Template\Compiler;
	use Edde\Common\Template\Template;
	use Edde\Common\Template\TemplateDirectory;
	use Edde\Common\Template\TemplateManager;
	use Edde\Common\Translator\Translator;
	use Edde\Common\Upgrade\AbstractUpgradeManager;
	use Edde\Common\Web\JavaScriptCompiler;
	use Edde\Common\Web\StyleSheetCompiler;
	use Edde\Common\Xml\XmlParser;
	use Edde\Ext\Cache\FlatFileCacheStorage;
	use Edde\Ext\Cache\InMemoryCacheStorage;
	use Edde\Ext\Converter\ConverterManagerConfigurator;
	use Edde\Ext\Database\Sqlite\SqliteDriver;
	use Edde\Ext\Database\Sqlite\SqliteDsn;
	use Edde\Ext\Resource\ResourceManagerConfigurator;
	use Edde\Ext\Router\RequestQueueConfigurator;
	use Edde\Ext\Router\RouterServiceConfigurator;
	use Edde\Ext\Template\CompilerConfigurator;

	class ContainerFactory extends Object {
		/**
		 * @param array $factoryList
		 *
		 * @return IFactory[]
		 * @throws FactoryException
		 */
		static public function createFactoryList(array $factoryList): array {
			$factories = [];
			foreach ($factoryList as $name => $factory) {
				$current = null;
				if ($factory instanceof \stdClass) {
					switch ($factory->type) {
						case 'instance':
							$current = new InstanceFactory($name, $factory->class, $factory->parameterList, null, $factory->cloneable);
							break;
						case 'exception':
							$current = new ExceptionFactory($name, $factory->message, $factory->class);
							break;
						case 'proxy':
							$current = new ProxyFactory($name, $factory->factory, $factory->method, $factory->parameterList);
							break;
					}
				} else if (is_string($factory) && strpos($factory, '::') !== false) {
					list($target, $method) = explode('::', $factory);
					$reflectionMethod = new \ReflectionMethod($target, $method);
					$current = new ProxyFactory($name, $target, $method);
					if ($reflectionMethod->isStatic()) {
						$current = new CallbackFactory($factory, $name);
					}
				} else if (is_string($name) && is_string($factory) && interface_exists($name)) {
					if (class_exists($factory)) {
						$current = new InterfaceFactory($name, $factory);
					} else if (interface_exists($factory)) {
						$current = new LinkFactory($name, $factory);
					}
				} else if ($factory instanceof IFactory) {
					$current = $factory;
				} else if (is_callable($factory)) {
					throw new FactoryException(sprintf('Closure is not supported in factory definition [%s].', $name));
				}
				if ($current === null) {
					throw new FactoryException(sprintf('Unsupported factory definition [%s; %s].', is_string($name) ? $name : (is_object($name) ? get_class($name) : gettype($name)), is_string($factory) ? $factory : (is_object($factory) ? get_class($factory) : gettype($factory))));
				}
				$factories[$name] = $current;
			}
			return $factories;
		}

		/**
		 * pure way how to simple create a system container
		 *
		 * @param array    $factoryList
		 * @param string[] $configuratorList
		 *
		 * @return IContainer
		 * @throws ContainerException
		 * @throws FactoryException
		 */
		static public function create(array $factoryList = [], array $configuratorList = []): IContainer {
			/**
			 * A young man and his date were parked on a back road some distance from town.
			 * They were about to have sex when the girl stopped.
			 * “I really should have mentioned this earlier, but I’m actually a hooker and I charge $20 for sex.”
			 * The man reluctantly paid her, and they did their thing.
			 * After a cigarette, the man just sat in the driver’s seat looking out the window.
			 * “Why aren’t we going anywhere?” asked the girl.
			 * “Well, I should have mentioned this before, but I’m actually a taxi driver, and the fare back to town is $25…”
			 */
			$container = new Container(new Cache(new InMemoryCacheStorage()));
			/**
			 * this trick ensures that container is properly configured when some internal dependency needs it while container is construction
			 */
			$containerConfigurator = $configuratorList[IContainer::class] = new ContainerConfigurator();
			$containerConfigurator->setFactoryList($factoryList = self::createFactoryList($factoryList));
			$containerConfigurator->setConfiguratorList($configuratorList);
			$container->addConfigurator($containerConfigurator);
			$container->setup();
			$container = $container->create(IContainer::class);
			$container->setup();
			return $container;
		}

		/**
		 * create a default container with set of services from Edde; they can be simply redefined
		 *
		 * @param array    $factoryList
		 * @param string[] $configuratorList
		 *
		 * @return IContainer
		 * @throws ContainerException
		 * @throws FactoryException
		 */
		static public function container(array $factoryList = [], array $configuratorList = []): IContainer {
			return self::create(array_merge(self::getDefaultFactoryList(), $factoryList), array_filter(array_merge(self::getDefaultConfiguratorList(), $configuratorList)));
		}

		/**
		 * create container and serialize the result into the file; if file exists, container is build from it
		 *
		 * @param array  $factoryList
		 * @param array  $configuratorList
		 * @param string $cacheId
		 *
		 * @return IContainer
		 * @throws ContainerException
		 * @throws FactoryException
		 */
		static public function cache(array $factoryList, array $configuratorList, string $cacheId): IContainer {
			if ($container = @file_get_contents($cacheId)) {
				/** @noinspection UnserializeExploitsInspection */
				return unserialize($container);
			}
			register_shutdown_function(function (IContainer $container, $cache) {
				file_put_contents($cache, serialize($container));
			}, $container = self::container($factoryList, $configuratorList), $cacheId);
			return $container;
		}

		/**
		 * create instance factory
		 *
		 * @param string $class
		 * @param array  $parameterList
		 * @param bool   $cloneable
		 *
		 * @return object
		 */
		static public function instance(string $class, array $parameterList, bool $cloneable = false) {
			return (object)[
				'type'          => __FUNCTION__,
				'class'         => $class,
				'parameterList' => $parameterList,
				'cloneable'     => $cloneable,
			];
		}

		/**
		 * special kind of factory which will thrown an exception of the given message; it's useful for say which internal dependencies are not met
		 *
		 * @param string      $message
|		 * @param string|null $class
		 *
		 * @return object
		 */
		static public function exception(string $message, string $class = null) {
			return (object)[
				'type'    => __FUNCTION__,
				'message' => $message,
				'class'   => $class ?: EddeException::class,
			];
		}

		/**
		 * create proxy call factory
		 *
		 * @param string $factory
		 * @param string $method
		 * @param array  $parameterList
		 *
		 * @return object
		 */
		static public function proxy(string $factory, string $method, array $parameterList) {
			return (object)[
				'type'          => __FUNCTION__,
				'factory'       => $factory,
				'method'        => $method,
				'parameterList' => $parameterList,
			];
		}

		static public function getDefaultFactoryList(): array {
			return [
				IContainer::class            => Container::class,
				IRootDirectory::class        => self::exception(sprintf('Root directory is not specified; please register [%s] interface.', IRootDirectory::class)),
				ITempDirectory::class        => self::proxy(IRootDirectory::class, 'directory', [
					'temp',
					TempDirectory::class,
				]),
				ICacheDirectory::class       => self::proxy(ITempDirectory::class, 'directory', [
					'cache',
					CacheDirectory::class,
				]),
				IAssetDirectory::class       => self::proxy(IRootDirectory::class, 'directory', [
					'.assets',
					AssetDirectory::class,
				]),
				ITemplateDirectory::class    => self::proxy(IAssetDirectory::class, 'directory', [
					'template',
					TemplateDirectory::class,
				]),
				ICrateDirectory::class       => self::proxy(IAssetDirectory::class, 'directory', [
					'crate',
					CrateDirectory::class,
				]),
				ILogDirectory::class         => self::proxy(IRootDirectory::class, 'directory', [
					'logs',
					LogDirectory::class,
				]),
				ISessionDirectory::class     => self::proxy(ITempDirectory::class, 'directory', [
					'session',
					SessionDirectory::class,
				]),
				IStorageDirectory::class     => self::proxy(IAssetDirectory::class, 'directory', [
					'storage',
					StorageDirectory::class,
				]),
				ICacheManager::class         => CacheManager::class,
				ICache::class                => ICacheManager::class,
				ICacheStorage::class         => FlatFileCacheStorage::class,
				IRuntime::class              => Runtime::class,
				IHttpResponse::class         => HttpResponse::class,
				IApplication::class          => Application::class,
				IRequestQueue::class         => RequestQueue::class,
				IRouterService::class        => RouterService::class,
				IRequest::class              => self::exception(sprintf('Do not use [%s] directly; to get reference to the current request, ask [%s]::getCurrentRequest().', IApplication::class)),
				IHttpRequest::class          => HttpRequest::class . '::createHttpRequest',
				IHttpResponse::class         => HttpResponse::class . '::createHttpResponse',
				IResponseManager::class      => ResponseManager::class,
				ILogService::class           => LogService::class,
				IXmlParser::class            => XmlParser::class,
				IConverterManager::class     => ConverterManager::class,
				IResourceManager::class      => ResourceManager::class,
				IResourceProvider::class     => IResourceManager::class,
				IStyleSheetCompiler::class   => StyleSheetCompiler::class,
				IJavaScriptCompiler::class   => JavaScriptCompiler::class,
				IStorage::class              => DatabaseStorage::class,
				IDriver::class               => SqliteDriver::class,
				IDsn::class                  => self::instance(SqliteDsn::class, ['storage.sqlite']),
				ICrate::class                => self::instance(Crate::class, [], true),
				ICrateFactory::class         => CrateFactory::class,
				ISchemaManager::class        => SchemaManager::class,
				IHttpClient::class           => HttpClient::class,
				IAclManager::class           => AclManager::class,
				IHtmlGenerator::class        => Html5Generator::class,
				ITemplateManager::class      => TemplateManager::class,
				ITemplate::class             => self::instance(Template::class, [], true),
				ICompiler::class             => Compiler::class,
				/**
				 * need to be defined
				 */
				IUpgradeManager::class       => self::exception(sprintf('Upgrade manager is not available; you must register [%s] interface; optionaly default [%s] implementation should help you.', IUpgradeManager::class, AbstractUpgradeManager::class)),
				ICryptEngine::class          => CryptEngine::class,
				IHostUrl::class              => self::exception(sprintf('Host url is not specified; you have to register [%s] interface.', IHostUrl::class)),
				ILinkFactory::class          => \Edde\Common\Link\LinkFactory::class,
				ISessionManager::class       => SessionManager::class,
				IIdentityManager::class      => IdentityManager::class,
				IIdentity::class             => IIdentityManager::class . '::createIdentity',
				IFingerprint::class          => self::exception(sprintf('You have to register or implement fingerprint interface [%s].', IFingerprint::class)),
				IContext::class              => self::exception(sprintf('You have to register implementation of [%s] specific for you application.', IContext::class)),
				IAuthenticatorManager::class => AuthenticatorManager::class,
				IAclManager::class           => AclManager::class,
				IAcl::class                  => Acl::class,
				ITranslator::class           => Translator::class,
				IAssetStorage::class         => AssetStorage::class,
			];
		}

		static public function getDefaultConfiguratorList(): array {
			return [
				IRouterService::class    => RouterServiceConfigurator::class,
				IRequestQueue::class     => RequestQueueConfigurator::class,
				/**
				 * We are using some custom resource providers, so we have to register them to resource manager and the current
				 * point how to get resources.
				 */
				IResourceManager::class  => ResourceManagerConfigurator::class,
				/**
				 * To enable general content exchange, we have to setup converter manager; it basically allows to do arbitrary
				 * data conversions for example json to array, xml file to INode, ... this component is kind of fundamental part
				 * of the framework.
				 */
				IConverterManager::class => ConverterManagerConfigurator::class,
				/**
				 * As other components, Template engine should be configured too; this will register default set of macros.
				 */
				ICompiler::class         => CompilerConfigurator::class,
			];
		}
	}

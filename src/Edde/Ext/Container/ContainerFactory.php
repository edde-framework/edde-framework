<?php
	declare(strict_types=1);

	namespace Edde\Ext\Container;

	use Edde\Api\Acl\IAclManager;
	use Edde\Api\Application\IApplication;
	use Edde\Api\Application\IRequest;
	use Edde\Api\Application\IResponseManager;
	use Edde\Api\Asset\IAssetDirectory;
	use Edde\Api\Cache\ICache;
	use Edde\Api\Cache\ICacheable;
	use Edde\Api\Cache\ICacheDirectory;
	use Edde\Api\Cache\ICacheManager;
	use Edde\Api\Cache\ICacheStorage;
	use Edde\Api\Container\ContainerException;
	use Edde\Api\Container\FactoryException;
	use Edde\Api\Container\IContainer;
	use Edde\Api\Container\IFactory;
	use Edde\Api\Converter\IConverterManager;
	use Edde\Api\Crate\ICrateDirectory;
	use Edde\Api\Crate\ICrateFactory;
	use Edde\Api\Database\IDriver;
	use Edde\Api\Database\IDsn;
	use Edde\Api\EddeException;
	use Edde\Api\File\IRootDirectory;
	use Edde\Api\File\ITempDirectory;
	use Edde\Api\Html\ITemplateDirectory;
	use Edde\Api\Http\Client\IHttpClient;
	use Edde\Api\Http\ICookieFactory;
	use Edde\Api\Http\ICookieList;
	use Edde\Api\Http\IHeaderFactory;
	use Edde\Api\Http\IHeaderList;
	use Edde\Api\Http\IHttpRequest;
	use Edde\Api\Http\IHttpResponse;
	use Edde\Api\Http\IPostFactory;
	use Edde\Api\Http\IPostList;
	use Edde\Api\Http\IRequestUrl;
	use Edde\Api\Http\IRequestUrlFactory;
	use Edde\Api\Log\ILogDirectory;
	use Edde\Api\Log\ILogService;
	use Edde\Api\Resource\IResourceManager;
	use Edde\Api\Router\IRouterService;
	use Edde\Api\Runtime\IRuntime;
	use Edde\Api\Schema\ISchemaFactory;
	use Edde\Api\Schema\ISchemaManager;
	use Edde\Api\Storage\IStorage;
	use Edde\Api\Template\IHelperSet;
	use Edde\Api\Template\IMacroSet;
	use Edde\Api\Template\ITemplateManager;
	use Edde\Api\Web\IJavaScriptCompiler;
	use Edde\Api\Web\IStyleSheetCompiler;
	use Edde\Api\Xml\IXmlParser;
	use Edde\Common\Asset\AssetDirectory;
	use Edde\Common\Cache\Cache;
	use Edde\Common\Cache\CacheDirectory;
	use Edde\Common\Crate\CrateDirectory;
	use Edde\Common\Database\Dsn;
	use Edde\Common\File\TempDirectory;
	use Edde\Common\Html\TemplateDirectory;
	use Edde\Common\Http\Client\HttpClient;
	use Edde\Common\Http\HeaderFactory;
	use Edde\Common\Http\HttpRequest;
	use Edde\Common\Http\HttpResponse;
	use Edde\Common\Log\LogDirectory;
	use Edde\Common\Object;
	use Edde\Ext\Cache\FlatFileCacheStorage;
	use Edde\Ext\Cache\InMemoryCacheStorage;
	use Edde\Ext\Database\Sqlite\SqliteDriver;
	use Edde\Ext\Template\DefaultMacroSet;
	use Edde\Service\Acl\AclManager;
	use Edde\Service\Application\Application;
	use Edde\Service\Application\ResponseManager;
	use Edde\Service\Cache\CacheManager;
	use Edde\Service\Container\Container;
	use Edde\Service\Converter\ConverterManager;
	use Edde\Service\Crate\CrateFactory;
	use Edde\Service\Database\DatabaseStorage;
	use Edde\Service\Http\CookieFactory;
	use Edde\Service\Http\PostFactory;
	use Edde\Service\Http\RequestUrlFactory;
	use Edde\Service\Log\LogService;
	use Edde\Service\Resource\ResourceManager;
	use Edde\Service\Router\RouterService;
	use Edde\Service\Runtime\Runtime;
	use Edde\Service\Schema\SchemaFactory;
	use Edde\Service\Schema\SchemaManager;
	use Edde\Service\Template\TemplateManager;
	use Edde\Service\Web\JavaScriptCompiler;
	use Edde\Service\Web\StyleSheetCompiler;
	use Edde\Service\Xml\XmlParser;

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
							$current = new InstanceFactory($name, $factory->class, $factory->parameterList);
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
				} else if ($factory instanceof IFactory || is_callable($factory)) {
					$current = $factory;
				} else if (is_callable($factory)) {
					throw new FactoryException(sprintf('Closure is not supported in factory definition [%s].', $name));
				} else if (is_object($factory)) {
					if ($factory instanceof ICacheable === false) {
						throw new FactoryException(sprintf('Class instances [%s] are not supported in factory definition [%s]. You can use [%s] interface to bypass this error.', get_class($factory), $name, ICacheable::class));
					}
					$current = new SerializableFactory($name, $factory);
				}
				if ($current === null) {
					throw new FactoryException(sprintf('Unsupported factory definition [%s; %s].', is_string($name) ? $name : (is_object($name) ? get_class($name) : gettype($name)), is_string($factory) ? $factory : (is_object($factory) ? get_class($factory) : gettype($factory))));
				}
				$factories[$name] = $current;
			}
			return $factories;
		}

		/**
		 * pure way how to simple create a system container using another container
		 *
		 * @param array    $factoryList
		 * @param string[] $configHandlerList
		 * @param string   $cacheId
		 *
		 * @return IContainer
		 */
		static public function create(array $factoryList = [], array $configHandlerList = [], string $cacheId = null): IContainer {
			/**
			 * A young man and his date were parked on a back road some distance from town.
			 * They were about to have sex when the girl stopped.
			 * “I really should have mentioned this earlier, but I’m actually a hooker and I charge $20 for sex.”
			 * The man reluctantly paid her, and they did their thing.
			 * After a cigarette, the man just sat in the driver’s seat looking out the window.
			 * “Why aren’t we going anywhere?” asked the girl.
			 * “Well, I should have mentioned this before, but I’m actually a taxi driver, and the fare back to town is $25…”
			 */
			/** @var $container IContainer */
			$container = new Container(new Cache(new InMemoryCacheStorage()));
			$closureList = array_filter($factoryList = self::createFactoryList($factoryList), function ($factory, $id) use (&$factoryList) {
				if (is_callable($factory)) {
					$factoryList[$id] = new ExceptionFactory((string)$id, sprintf('Using placeholder factory instead of callback [%s].', $id), EddeException::class);
					return true;
				}
				return false;
			}, ARRAY_FILTER_USE_BOTH);
			$container->registerFactoryList($factoryList);
			$container = $container->create(IContainer::class);
			if ($cacheId !== null) {
				$container->getCache()
					->setNamespace($cacheId);
			}
			$container->registerFactoryList($factoryList);
			foreach ($configHandlerList as $name => $configHandler) {
				foreach ($configHandler as $config) {
					$container->registerConfigHandler($name, $container->create($config, [], __METHOD__));
				}
			}
			foreach ($factoryList as $factory) {
				$container->autowire($factory);
			}
			foreach ($closureList as $id => $closure) {
				$container->registerFactory(new InstanceFactory((string)$id, get_class($instance = $container->call($closure, [], 'factory/' . $id)), [], $instance), $id);
			}
			return $container;
		}

		/**
		 * create a default container with set of services from Edde; they can be simply redefined
		 *
		 * @param array    $factoryList
		 * @param string[] $configHandlerList
		 * @param string   $cacheId
		 *
		 * @return IContainer
		 */
		static public function container(array $factoryList = [], array $configHandlerList = [], string $cacheId = null): IContainer {
			return self::create(array_merge([
				IContainer::class => Container::class,
				ICacheStorage::class => InMemoryCacheStorage::class,
				ICacheManager::class => CacheManager::class,
				ICache::class => ICacheManager::class,
			], $factoryList), array_merge([], $configHandlerList), $cacheId);
		}

		/**
		 * create container and serialize the result into the file; if file exists, container is build from it
		 *
		 * @param array  $factoryList
		 * @param array  $configHandlerList
		 * @param string $cacheId
		 *
		 * @return IContainer
		 * @throws ContainerException
		 * @throws FactoryException
		 */
		static public function cache(array $factoryList, array $configHandlerList, string $cacheId): IContainer {
			if ($container = @file_get_contents($cacheId)) {
				/** @noinspection UnserializeExploitsInspection */
				return unserialize($container);
			}
			register_shutdown_function(function (IContainer $container, $cache) {
				file_put_contents($cache, serialize($container));
			}, $container = self::container($factoryList, $configHandlerList, $cacheId), $cacheId);
			return $container;
		}

		/**
		 * create instance factory
		 *
		 * @param string $class
		 * @param array  $parameterList
		 *
		 * @return object
		 */
		static public function instance(string $class, array $parameterList) {
			return (object)[
				'type' => __FUNCTION__,
				'class' => $class,
				'parameterList' => $parameterList,
			];
		}

		/**
		 * special kind of factory which will thrown an exception of the given message; it's useful for say which internal dependencies are not met
		 *
		 * @param string $message
		 *
		 * @return object
		 */
		static public function exception(string $message, string $class = null) {
			return (object)[
				'type' => __FUNCTION__,
				'message' => $message,
				'class' => $class ?: EddeException::class,
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
				'type' => __FUNCTION__,
				'factory' => $factory,
				'method' => $method,
				'parameterList' => $parameterList,
			];
		}

		static public function getDefaultFactoryList(): array {
			return [
				IRootDirectory::class => self::exception(sprintf('Root directory is not specified; please register [%s] interface.', IRootDirectory::class)),
				ITempDirectory::class => self::proxy(IRootDirectory::class, 'directory', [
					'temp',
					TempDirectory::class,
				]),
				ICacheDirectory::class => self::proxy(ITempDirectory::class, 'directory', [
					'cache',
					CacheDirectory::class,
				]),
				IAssetDirectory::class => self::proxy(IRootDirectory::class, 'directory', [
					'.assets',
					AssetDirectory::class,
				]),
				ITemplateDirectory::class => self::proxy(IAssetDirectory::class, 'directory', [
					'template',
					TemplateDirectory::class,
				]),
				ICrateDirectory::class => self::proxy(IAssetDirectory::class, 'directory', [
					'crate',
					CrateDirectory::class,
				]),
				ILogDirectory::class => self::proxy(IRootDirectory::class, 'directory', [
					'logs',
					LogDirectory::class,
				]),
				ICacheStorage::class => FlatFileCacheStorage::class,
				IRuntime::class => Runtime::class,
				IHttpResponse::class => HttpResponse::class,
				IApplication::class => Application::class,
				ILogService::class => LogService::class,
				IRouterService::class => RouterService::class,
				IRequest::class => IRouterService::class . '::createRequest',
				IPostFactory::class => PostFactory::class,
				IPostList::class => IPostFactory::class . '::create',
				ICookieFactory::class => CookieFactory::class,
				ICookieList::class => ICookieFactory::class . '::create',
				IRequestUrlFactory::class => RequestUrlFactory::class,
				IRequestUrl::class => IRequestUrlFactory::class . '::create',
				IHeaderFactory::class => HeaderFactory::class,
				IHeaderList::class => IHeaderFactory::class . '::create',
				IHttpRequest::class => HttpRequest::class,
				IResponseManager::class => ResponseManager::class,
				IXmlParser::class => XmlParser::class,
				IConverterManager::class => ConverterManager::class,
				IResourceManager::class => ResourceManager::class,
				IStyleSheetCompiler::class => StyleSheetCompiler::class,
				IJavaScriptCompiler::class => JavaScriptCompiler::class,
				ITemplateManager::class => TemplateManager::class,
				IMacroSet::class => DefaultMacroSet::class . '::macroSet',
				IHelperSet::class => DefaultMacroSet::class . '::helperSet',
				IStorage::class => DatabaseStorage::class,
				IDriver::class => SqliteDriver::class,
				IDsn::class => function (IAssetDirectory $assetDirectory) {
					return new Dsn('sqlite:' . $assetDirectory->filename('storage.sqlite'));
				},
				ICrateFactory::class => CrateFactory::class,
				ISchemaFactory::class => SchemaFactory::class,
				ISchemaManager::class => SchemaManager::class,
				IHttpClient::class => HttpClient::class,
				IAclManager::class => AclManager::class,
			];
		}
	}

<?php
	declare(strict_types=1);

	namespace Edde\Ext\Container;

	use Edde\Api\Application\IApplication;
	use Edde\Api\Container\Exception\ContainerException;
	use Edde\Api\Container\Exception\FactoryException;
	use Edde\Api\Container\IContainer;
	use Edde\Api\Container\IFactory;
	use Edde\Api\EddeException;
	use Edde\Api\Http\IHostUrl;
	use Edde\Api\Http\IHttpService;
	use Edde\Api\Log\ILogService;
	use Edde\Api\Router\IRouterService;
	use Edde\Api\Runtime\IRuntime;
	use Edde\Api\Utils\IHttpUtils;
	use Edde\Api\Utils\IStringUtils;
	use Edde\Common\Application\Application;
	use Edde\Common\Container\Container;
	use Edde\Common\Container\Factory\CallbackFactory;
	use Edde\Common\Container\Factory\ClassFactory;
	use Edde\Common\Container\Factory\ExceptionFactory;
	use Edde\Common\Container\Factory\InstanceFactory;
	use Edde\Common\Container\Factory\InterfaceFactory;
	use Edde\Common\Container\Factory\LinkFactory;
	use Edde\Common\Container\Factory\ProxyFactory;
	use Edde\Common\Http\HostUrl;
	use Edde\Common\Http\HttpService;
	use Edde\Common\Log\LogService;
	use Edde\Common\Object\Object;
	use Edde\Common\Router\RouterService;
	use Edde\Common\Runtime\Runtime;
	use Edde\Common\Utils\HttpUtils;
	use Edde\Common\Utils\StringUtils;

	class ContainerFactory extends Object {
		/**
		 * @param array $factoryList
		 *
		 * @return IFactory[]
		 * @throws \Edde\Api\Container\Exception\FactoryException
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
							$current = new ExceptionFactory($name, $factory->class, $factory->message);
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
				} else if (is_string($name) && is_string($factory) && (interface_exists($factory) || class_exists($factory))) {
					if (class_exists($factory)) {
						$current = new InterfaceFactory($name, $factory);
					} else if (interface_exists($factory)) {
						$current = new LinkFactory($name, $factory);
					}
				} else if ($factory instanceof IFactory) {
					$current = $factory;
				} else if (is_callable($factory)) {
					throw new \Edde\Api\Container\Exception\FactoryException(sprintf('Closure is not supported in factory definition [%s].', $name));
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
			/** @var $container IContainer */
			$container = new Container();
			/**
			 * this trick ensures that container is properly configured when some internal dependency needs it while container is construction
			 */
			$containerConfigurator = $configuratorList[IContainer::class] = new ContainerConfigurator($factoryList = self::createFactoryList($factoryList), $configuratorList);
			$container->addConfigurator($containerConfigurator);
			$container->setup();
			$container = $container->create(IContainer::class);
			$container->addConfigurator($containerConfigurator);
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
		 * @throws \Edde\Api\Container\Exception\ContainerException
		 * @throws \Edde\Api\Container\Exception\FactoryException
		 */
		static public function container(array $factoryList = [], array $configuratorList = []): IContainer {
			return self::create(array_merge(self::getDefaultFactoryList(), $factoryList), array_filter(array_merge(self::getDefaultConfiguratorList(), $configuratorList)));
		}

		/**
		 * shortcut for autowiring (for example in tests, ...)
		 *
		 * @param mixed $instance
		 * @param array $factoryList
		 * @param array $configuratorList
		 *
		 * @return IContainer
		 * @throws ContainerException
		 * @throws FactoryException
		 */
		static public function inject($instance, array $factoryList = [], array $configuratorList = []): IContainer {
			$container = self::container(empty($factoryList) ? [new ClassFactory()] : $factoryList, $configuratorList);
			$container->inject($instance);
			return $container;
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
				'type' => __FUNCTION__,
				'class' => $class,
				'parameterList' => $parameterList,
				'cloneable' => $cloneable,
			];
		}

		/**
		 * special kind of factory which will thrown an exception of the given message; it's useful for say which internal dependencies are not met
		 *
		 * @param string      $message
		 * @param string|null $class
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
				/**
				 * utils
				 */
				IHttpUtils::class => HttpUtils::class,
				IStringUtils::class => StringUtils::class,

				/**
				 * container implementation
				 */
				IContainer::class => Container::class,

				/**
				 * runtime info provider
				 */
				IRuntime::class => Runtime::class,

				/**
				 * if needed, host url provider (host name is used for absolute links)
				 */
				IHostUrl::class => HostUrl::class . '::factory',

				/**
				 * log support
				 */
				ILogService::class => LogService::class,

				/**
				 * user request into protocol element translation
				 */
				IRouterService::class => RouterService::class,

				/**
				 * general service for http request/response
				 */
				IHttpService::class => HttpService::class,

				/**
				 * an application handles lifecycle workflow
				 */
				IApplication::class => Application::class,

				/**
				 * magical factory for an application execution
				 */
				'run' => IApplication::class . '::run',
			];
		}

		static public function getDefaultConfiguratorList(): array {
			return [];
		}
	}

<?php
	declare(strict_types = 1);

	namespace Edde\Common\Runtime;

	use Edde\Api\Cache\ICacheManager;
	use Edde\Api\Cache\ICacheStorage;
	use Edde\Api\Container\FactoryException;
	use Edde\Api\Container\IContainer;
	use Edde\Api\Container\IDependencyFactory;
	use Edde\Api\Container\IFactory;
	use Edde\Api\Container\IFactoryManager;
	use Edde\Api\Deffered\IDeffered;
	use Edde\Api\Runtime\IRuntime;
	use Edde\Api\Runtime\RuntimeException;
	use Edde\Common\Cache\CacheManager;
	use Edde\Common\Container\Container;
	use Edde\Common\Container\DependencyFactory;
	use Edde\Common\Container\Factory\FactoryFactory;
	use Edde\Common\Container\FactoryManager;
	use Edde\Common\Deffered\AbstractDeffered;
	use Edde\Common\Event\EventTrait;
	use Edde\Ext\Cache\InMemoryCacheStorage;
	use Edde\Ext\Container\ContainerFactory;

	/**
	 * Low level class responsible for basic system preparation. If application is not used, this should be present
	 * all the times.
	 */
	class Runtime extends AbstractDeffered implements IRuntime {
		use EventTrait;

		/**
		 * @var IFactory[]
		 */
		protected $factoryList = [];

		/**
		 * @inheritdoc
		 * @throws FactoryException
		 */
		public function registerFactoryList(array $factoryList): IRuntime {
			$this->factoryList = FactoryFactory::createList(array_merge($this->factoryList, $factoryList));
			return $this;
		}

		/**
		 * @inheritdoc
		 * @throws RuntimeException
		 */
		public function deffered(string $name, callable $onSetup): IRuntime {
			if (isset($this->factoryList[$name]) === false) {
				throw new RuntimeException(sprintf('Cannot use deffered setup on unknown factory [%s].', $name));
			}
			$this->factoryList[$name]->deffered(function (IContainer $container, $instance) use ($onSetup) {
				if (($instance instanceof IDeffered) === false) {
					throw new RuntimeException(sprintf('Deffered class must implement [%s] interface.', IDeffered::class));
				}
				/** @var $instance IDeffered */
				$instance->onDeffered(function () use ($container, $onSetup, $instance) {
					return $container->call($onSetup, $instance);
				});
			});
			return $this;
		}

		/**
		 * @inheritdoc
		 * @throws FactoryException
		 */
		public function createContainer(): IContainer {
			return ContainerFactory::container(array_merge([
				IContainer::class => Container::class,
				IFactoryManager::class => FactoryManager::class,
				IDependencyFactory::class => DependencyFactory::class,
				ICacheManager::class => CacheManager::class,
				ICacheStorage::class => InMemoryCacheStorage::class,
			], $this->factoryList));
		}

		/**
		 * @inheritdoc
		 */
		public function run(callable $callback) {
			$this->use();
			$container = $this->createContainer();
			return $container->call($callback);
		}

		/**
		 * @inheritdoc
		 */
		public function isConsoleMode(): bool {
			return php_sapi_name() === 'cli';
		}
	}

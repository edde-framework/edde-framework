<?php
	declare(strict_types = 1);

	namespace Edde\Common\Runtime;

	use Edde\Api\Cache\ICacheDirectory;
	use Edde\Api\Cache\ICacheManager;
	use Edde\Api\Cache\ICacheStorage;
	use Edde\Api\Container\FactoryException;
	use Edde\Api\Container\IContainer;
	use Edde\Api\Container\IDependencyFactory;
	use Edde\Api\Container\IFactory;
	use Edde\Api\Container\IFactoryManager;
	use Edde\Api\Deffered\IDeffered;
	use Edde\Api\File\IRootDirectory;
	use Edde\Api\File\ITempDirectory;
	use Edde\Api\Runtime\IRuntime;
	use Edde\Api\Runtime\RuntimeException;
	use Edde\Common\Cache\CacheDirectory;
	use Edde\Common\Cache\CacheManager;
	use Edde\Common\Container\Container;
	use Edde\Common\Container\DependencyFactory;
	use Edde\Common\Container\Factory\FactoryFactory;
	use Edde\Common\Container\FactoryManager;
	use Edde\Common\Deffered\AbstractDeffered;
	use Edde\Common\Event\EventTrait;
	use Edde\Common\File\TempDirectory;
	use Edde\Ext\Cache\InMemoryCacheStorage;
	use Edde\Ext\Container\ContainerFactory;
	use Edde\Framework;

	/**
	 * Low level class responsible for basic system preparation. If application is not used, this should be present
	 * all the times.
	 */
	class Runtime extends AbstractDeffered implements IRuntime {
		use EventTrait;

		/**
		 * @var IFactory[]
		 */
		protected $factoryList;

		/**
		 * @param IFactory[] $factoryList
		 */
		public function __construct(array $factoryList = []) {
			$this->factoryList = $factoryList;
		}

		/**
		 * @inheritdoc
		 * @throws FactoryException
		 */
		public function registerFactoryList(array $factoryList): IRuntime {
			$this->factoryList = array_merge($this->factoryList, $factoryList);
			return $this;
		}

		/**
		 * @inheritdoc
		 * @throws RuntimeException
		 */
		public function deffered(string $name, callable $onSetup): IRuntime {
			$this->use();
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
			$this->use();
			return ContainerFactory::container(array_merge([
				new Framework(),
				IContainer::class => Container::class,
				IFactoryManager::class => FactoryManager::class,
				IDependencyFactory::class => DependencyFactory::class,
				ITempDirectory::class => function (IRootDirectory $rootDirectory) {
					return $rootDirectory->directory('temp', TempDirectory::class);
				},
				ICacheDirectory::class => function (ITempDirectory $tempDirectory) {
					return $tempDirectory->directory('cache', CacheDirectory::class);
				},
				ICacheManager::class => CacheManager::class,
				ICacheStorage::class => InMemoryCacheStorage::class,
			], $this->factoryList));
		}

		/**
		 * @inheritdoc
		 * @throws FactoryException
		 */
		public function run(callable $callback) {
			$this->use();
			return $this->createContainer()
				->call($callback);
		}

		/**
		 * @inheritdoc
		 */
		public function isConsoleMode(): bool {
			return php_sapi_name() === 'cli';
		}

		protected function prepare() {
			parent::prepare();
			$this->factoryList = FactoryFactory::createList($this->factoryList);
		}
	}

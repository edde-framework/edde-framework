<?php
	declare(strict_types = 1);

	namespace Edde\Common\Runtime;

	use Edde\Api\Container\ContainerException;
	use Edde\Api\Container\FactoryException;
	use Edde\Api\Container\IContainer;
	use Edde\Api\Container\IFactory;
	use Edde\Api\Deffered\IDeffered;
	use Edde\Api\Runtime\IModule;
	use Edde\Api\Runtime\IRuntime;
	use Edde\Api\Runtime\RuntimeException;
	use Edde\Common\AbstractObject;
	use Edde\Common\Container\Factory\FactoryFactory;
	use Edde\Common\Event\EventTrait;
	use Edde\Common\Runtime\Event\ContainerEvent;
	use Edde\Common\Runtime\Event\ExceptionEvent;
	use Edde\Common\Runtime\Event\SetupEvent;
	use Edde\Common\Runtime\Event\ShutdownEvent;
	use Edde\Ext\Container\ContainerFactory;

	/**
	 * Low level class responsible for basic system preparation. If application is not used, this should be present
	 * all the times.
	 */
	class Runtime extends AbstractObject implements IRuntime {
		use EventTrait;
		/**
		 * @var IFactory[]
		 */
		protected $factoryList = [];
		/**
		 * @var IFactory[]
		 */
		protected $runtimeList = [];

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
			$this->runtimeList = array_merge($this->runtimeList, $factoryList);
			return $this;
		}

		/**
		 * @inheritdoc
		 */
		public function module(IModule $module): IRuntime {
			$this->listen($module);
			return $this;
		}

		/**
		 * @inheritdoc
		 */
		public function moduleList(array $moduleList): IRuntime {
			foreach ($moduleList as $module) {
				$this->module($module);
			}
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
				$instance->registerOnUse(function () use ($container, $onSetup, $instance) {
					return $container->call($onSetup, $instance);
				});
			});
			return $this;
		}

		/**
		 * @inheritdoc
		 * @throws FactoryException
		 * @throws ContainerException
		 */
		public function createContainer(): IContainer {
			return ContainerFactory::create($this->factoryList = FactoryFactory::createList(array_merge([IRuntime::class => $this,], $this->runtimeList, $this->factoryList)));
		}

		/**
		 * @inheritdoc
		 * @throws FactoryException
		 * @throws ContainerException
		 * @throws \Exception
		 */
		public function run(callable $callback) {
			try {
				$this->event(new SetupEvent($this));
				$this->event(new ContainerEvent($this, $container = $this->createContainer()));
				$this->event(new ShutdownEvent($container, $result = $container->call($callback)));
				return $result;
			} catch (\Exception $exception) {
				$this->event(new ExceptionEvent($exception));
				throw $exception;
			}
		}

		/**
		 * @inheritdoc
		 */
		public function isConsoleMode(): bool {
			return php_sapi_name() === 'cli';
		}
	}

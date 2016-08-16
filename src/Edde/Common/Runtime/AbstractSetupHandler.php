<?php
	declare(strict_types = 1);

	namespace Edde\Common\Runtime;

	use Edde\Api\Container\IContainer;
	use Edde\Api\Container\IFactory;
	use Edde\Api\Runtime\ISetupHandler;
	use Edde\Api\Runtime\RuntimeException;
	use Edde\Api\Usable\IUsable;
	use Edde\Common\AbstractObject;
	use Edde\Common\Container\Factory\FactoryFactory;

	abstract class AbstractSetupHandler extends AbstractObject implements ISetupHandler {
		/**
		 * @var IFactory[]
		 */
		protected $factoryList = [];
		/**
		 * @var callable
		 */
		protected $factoryFallback;

		public function registerFactoryList(array $fatoryList) {
			$this->factoryList = FactoryFactory::createList(array_merge($this->factoryList, $fatoryList));
			return $this;
		}

		public function registerFactoryFallback(callable $callback) {
			$this->factoryFallback = $callback;
			return $this;
		}

		public function onSetup($name, callable $onSetup): AbstractSetupHandler {
			if (isset($this->factoryList[$name]) === false) {
				return $this;
			}
			$this->onFactorySetup($this->factoryList[$name], $onSetup);
			return $this;
		}

		private function onFactorySetup(IFactory $factory, callable $callback) {
			$factory->onSetup(function (IContainer $container, $instance) use ($callback) {
				if (($instance instanceof IUsable) === false) {
					throw new RuntimeException(sprintf('Deffered class must implement [%s] interface.', IUsable::class));
				}
				/** @var $instance IUsable */
				$instance->onSetup(function () use ($container, $callback, $instance) {
					return $container->call($callback, $instance);
				});
			});
		}

		protected function factory($class, callable $onSetup) {
			return [
				$class,
				function (IFactory $factory) use ($onSetup) {
					$this->onFactorySetup($factory, $onSetup);
				},
			];
		}
	}

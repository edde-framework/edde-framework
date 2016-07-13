<?php
	namespace Edde\Common\Runtime;

	use Edde\Api\Container\IContainer;
	use Edde\Api\Container\IFactory;
	use Edde\Api\Runtime\ISetupHandler;
	use Edde\Api\Usable\IUsable;
	use Edde\Common\AbstractObject;

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
			$this->factoryList = array_merge($this->factoryList, $fatoryList);
			return $this;
		}

		public function registerFactoryFallback(callable $callback) {
			$this->factoryFallback = $callback;
			return $this;
		}

		protected function factory($class, callable $onSetup) {
			return [
				$class,
				function (IFactory $factory) use ($onSetup) {
					$factory->onSetup(function (IContainer $container, $instance) use ($onSetup) {
						if ($instance instanceof IUsable) {
							$instance->onSetup(function () use ($container, $onSetup, $instance) {
								return $container->call($onSetup, $instance);
							});
							return;
						}
						$container->call($onSetup);
					});
				},
			];
		}
	}

<?php
	declare(strict_types = 1);

	namespace Edde\Common\Runtime;

	use Edde\Api\Container\FactoryException;
	use Edde\Api\Container\IContainer;
	use Edde\Api\Container\IFactory;
	use Edde\Api\Runtime\ISetupHandler;
	use Edde\Api\Runtime\RuntimeException;
	use Edde\Api\Usable\IUsable;
	use Edde\Common\AbstractObject;
	use Edde\Common\Container\Factory\FactoryFactory;

	/**
	 * Common class for all setup handlers.
	 */
	abstract class AbstractSetupHandler extends AbstractObject implements ISetupHandler {
		/**
		 * @var IFactory[]
		 */
		protected $factoryList = [];

		/**
		 * @inheritdoc
		 * @throws FactoryException
		 */
		public function registerFactoryList(array $fatoryList): ISetupHandler {
			$this->factoryList = FactoryFactory::createList(array_merge($this->factoryList, $fatoryList));
			return $this;
		}

		/**
		 * @inheritdoc
		 * @throws RuntimeException
		 */
		public function deffered(string $name, callable $onSetup): ISetupHandler {
			if (isset($this->factoryList[$name]) === false) {
				throw new RuntimeException(sprintf('Cannot use deffered setup on unknown factory [%s].', $name));
			}
			$this->factoryList[$name]->onSetup(function (IContainer $container, $instance) use ($onSetup) {
				if (($instance instanceof IUsable) === false) {
					throw new RuntimeException(sprintf('Deffered class must implement [%s] interface.', IUsable::class));
				}
				/** @var $instance IUsable */
				$instance->onSetup(function () use ($container, $onSetup, $instance) {
					return $container->call($onSetup, $instance);
				});
			});
			return $this;
		}
	}

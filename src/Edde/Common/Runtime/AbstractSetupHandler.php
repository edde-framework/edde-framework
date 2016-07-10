<?php
	namespace Edde\Common\Runtime;

	use Edde\Api\Container\IFactory;
	use Edde\Api\Runtime\ISetupHandler;
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
	}

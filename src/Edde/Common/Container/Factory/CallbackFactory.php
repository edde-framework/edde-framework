<?php
	declare(strict_types = 1);

	namespace Edde\Common\Container\Factory;

	use Edde\Api\Callback\IParameter;
	use Edde\Api\Container\IContainer;
	use Edde\Common\Callback\CallbackUtils;

	class CallbackFactory extends AbstractFactory {
		/**
		 * @var callable
		 */
		private $callback;
		/**
		 * @var IParameter[]
		 */
		private $parameterList;

		/**
		 * @param string $name
		 * @param callable $callback
		 * @param bool $singleton
		 * @param bool $cloneable
		 */
		public function __construct($name, callable $callback, $singleton = true, $cloneable = false) {
			parent::__construct($name, $singleton, $cloneable);
			$this->callback = $callback;
		}

		public function getParameterList() {
			if ($this->parameterList === null) {
				$this->parameterList = CallbackUtils::getParameterList($this->callback);
			}
			return $this->parameterList;
		}

		public function factory(array $parameterList, IContainer $container) {
			return call_user_func_array($this->callback, $parameterList);
		}
	}

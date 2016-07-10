<?php
	namespace Edde\Common\Callback;

	use Edde\Api\Callback\ICallback;
	use Edde\Api\Callback\IParameter;
	use Edde\Common\AbstractObject;

	class Callback extends AbstractObject implements ICallback {
		/**
		 * @var callable
		 */
		private $callback;

		/**
		 * @param callable $callback
		 */
		public function __construct(callable $callback) {
			$this->callback = $callback;
		}

		public function getCallback() {
			return $this->callback;
		}

		/**
		 * @return IParameter[]
		 */
		public function getParameterList() {
			return CallbackUtils::getParameterList($this->callback);
		}

		public function __invoke(...$parameterList) {
			return $this->invoke(...$parameterList);
		}

		public function invoke(...$parameterList) {
			return call_user_func_array($this->callback, $parameterList);
		}
	}

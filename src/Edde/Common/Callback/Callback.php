<?php
	declare(strict_types = 1);

	namespace Edde\Common\Callback;

	use Edde\Api\Callback\ICallback;
	use Edde\Common\AbstractObject;

	class Callback extends AbstractObject implements ICallback {
		/**
		 * @var callable
		 */
		protected $callback;
		protected $parameterList = null;

		/**
		 * @param callable $callback
		 */
		public function __construct(callable $callback) {
			$this->callback = $callback;
		}

		public function getCallback(): callable {
			return $this->callback;
		}

		public function getParameterCount(): int {
			return count($this->getParameterList());
		}

		public function getParameterList(): array {
			if ($this->parameterList === null) {
				$this->parameterList = CallbackUtils::getParameterList($this->callback);
			}
			return $this->parameterList;
		}

		public function __invoke(...$parameterList) {
			return $this->invoke(...$parameterList);
		}

		public function invoke(...$parameterList) {
			return call_user_func_array($this->callback, $parameterList);
		}
	}

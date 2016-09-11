<?php
	declare(strict_types = 1);

	use Edde\Api\Application\IErrorControl;
	use Edde\Api\Application\IRequest;
	use Edde\Api\Control\ControlException;
	use Edde\Common\Control\AbstractControl;

	class SomeControl extends AbstractControl {
		protected $throw = false;

		public function throw() {
			$this->throw = true;
		}

		public function executeThisMethod($poo) {
			if ($this->throw) {
				throw new ControlException('some error');
			}
			return $poo;
		}
	}

	class ForbiddenControl {
	}

	class SomeErrorControl extends AbstractControl implements IErrorControl {
		protected $exception;

		public function exception(\Exception $e) {
			return $this->exception = $e;
		}

		/**
		 * @return \Exception
		 */
		public function getException() {
			return $this->exception;
		}
	}

	class SomeRequest implements IRequest {
		public $class;
		public $method;
		public $parameters;

		public function getType(): string {
			return 'foo';
		}

		public function getClass(): string {
			return $this->class;
		}

		public function getMethod(): string {
			return $this->method;
		}

		public function getParameterList(): array {
			return $this->parameters;
		}
	}

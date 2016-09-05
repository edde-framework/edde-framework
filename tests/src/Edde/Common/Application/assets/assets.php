<?php
	declare(strict_types = 1);

	use Edde\Api\Application\IErrorControl;
	use Edde\Api\Control\ControlException;
	use Edde\Api\Router\IRoute;
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

	class SomeRoute implements IRoute {
		public $class;
		public $method;
		public $parameters;

		public function getClass() {
			return $this->class;
		}

		public function getMethod() {
			return $this->method;
		}

		public function getParameterList() {
			return $this->parameters;
		}

		public function getCrateList() {
			return [];
		}
	}

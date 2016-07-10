<?php
	namespace Edde\Common\Router;

	use Edde\Api\Crate\ICrate;
	use Edde\Api\Router\IRoute;
	use Edde\Common\AbstractObject;

	class Route extends AbstractObject implements IRoute {
		/**
		 * @var string
		 */
		private $class;
		/**
		 * @var string
		 */
		private $method;
		/**
		 * @var array
		 */
		private $parameterList;
		/**
		 * @var ICrate
		 */
		private $crate;

		/**
		 * @param string $class
		 * @param string $method
		 * @param array $parameterList
		 * @param ICrate $crate
		 */
		public function __construct($class, $method, array $parameterList = [], ICrate $crate = null) {
			$this->class = $class;
			$this->method = $method;
			$this->parameterList = $parameterList;
			$this->crate = $crate;
		}

		public function getClass() {
			return $this->class;
		}

		public function getMethod() {
			return $this->method;
		}

		public function getParameterList() {
			return $this->parameterList;
		}

		public function getCrate() {
			return $this->crate;
		}
	}

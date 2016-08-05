<?php
	declare(strict_types = 1);

	namespace Edde\Common\Container\Factory;

	use Edde\Api\Callback\IParameter;
	use Edde\Api\Container\IContainer;
	use Edde\Common\Callback\CallbackUtils;
	use ReflectionClass;

	/**
	 * Simple constructor-based class factory.
	 */
	class ClassFactory extends AbstractFactory {
		/**
		 * @var string
		 */
		protected $class;
		/**
		 * @var IParameter[]
		 */
		protected $parameterList;

		/**
		 * @param string $name
		 * @param string $class
		 * @param bool $singleton
		 * @param bool $cloneable
		 */
		public function __construct($name, $class, $singleton = true, $cloneable = false) {
			parent::__construct($name, $singleton, $cloneable);
			$this->class = $class;
		}

		public function getParameterList() {
			if ($this->parameterList === null) {
				$this->parameterList = CallbackUtils::getParameterList($this->class);
			}
			return $this->parameterList;
		}

		public function factory(array $parameterList, IContainer $container) {
			return (new ReflectionClass($this->class))->newInstanceArgs($parameterList);
		}
	}

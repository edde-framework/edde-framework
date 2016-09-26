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
	class ReflectionFactory extends AbstractFactory {
		/**
		 * @var string
		 */
		protected $class;
		/**
		 * @var IParameter[][]
		 */
		protected $parameterList;

		/**
		 * @param string $name
		 * @param string $class
		 * @param bool $singleton
		 * @param bool $cloneable
		 */
		public function __construct(string $name, string $class, bool $singleton = true, bool $cloneable = false) {
			parent::__construct($name, $singleton, $cloneable);
			$this->class = $class;
		}

		/**
		 * @inheritdoc
		 */
		public function getParameterList(string $name = null): array {
			if (isset($this->parameterList[$name]) === false) {
				$this->parameterList[$name] = CallbackUtils::getParameterList($name ?: $this->class);
			}
			return $this->parameterList[$name];
		}

		/**
		 * @inheritdoc
		 */
		public function factory(string $name, array $parameterList, IContainer $container) {
			$reflactionClass = new ReflectionClass($this->class);
			if (empty($parameterList)) {
				return $reflactionClass->newInstance();
			}
			return $reflactionClass->newInstanceArgs($parameterList);
		}
	}

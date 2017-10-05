<?php
	declare(strict_types=1);

	namespace Edde\Common\Container\Factory;

	use Edde\Api\Container\IContainer;
	use Edde\Api\Container\IReflection;
	use Edde\Common\Container\Parameter;
	use Edde\Common\Container\Reflection;

	class CallbackFactory extends AbstractFactory {
		/**
		 * @var callable
		 */
		protected $callback;
		/**
		 * @var string
		 */
		protected $name;

		/**
		 * @param string   $name
		 * @param callable $callback
		 */
		public function __construct(callable $callback, string $name = null) {
			$this->callback = $callback;
			$this->name = $name;
		}

		/**
		 * @inheritdoc
		 * @throws ReflectionException
		 */
		public function canHandle(IContainer $container, string $dependency): bool {
			if ($this->name === null) {
				$this->name = (string)ReflectionUtils::getMethodReflection($this->callback)->getReturnType();
			}
			return $dependency === $this->name;
		}

		/**
		 * @inheritdoc
		 */
		public function getReflection(IContainer $container, string $dependency = null): IReflection {
			$parameterList = [];
			foreach (ReflectionUtils::getParameterList($this->callback) as $reflectionParameter) {
				$parameterList[] = new Parameter($reflectionParameter->getName(), $reflectionParameter->isOptional(), ($class = $reflectionParameter->getClass()) ? $class->getName() : null);
			}
			return new Reflection($parameterList);
		}

		/**
		 * @inheritdoc
		 */
		public function factory(IContainer $container, array $parameterList, IReflection $dependency, string $name = null) {
			return call_user_func_array($this->callback, $this->parameters($container, $parameterList, $dependency));
		}
	}

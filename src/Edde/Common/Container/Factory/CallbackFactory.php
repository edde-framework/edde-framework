<?php
	declare(strict_types=1);
	namespace Edde\Common\Container\Factory;

	use Closure;
	use Edde\Api\Container\Exception\ReflectionException;
	use Edde\Api\Container\IContainer;
	use Edde\Api\Container\IReflection;
	use Edde\Common\Container\Parameter;
	use Edde\Common\Container\Reflection;
	use ReflectionFunction;

	class CallbackFactory extends AbstractFactory {
		/**
		 * @var callable
		 */
		protected $callback;
		/**
		 * @var \Closure
		 */
		protected $closure;
		/**
		 * @var \ReflectionFunction
		 */
		protected $reflectionFunction;
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
			$this->reflectionFunction = new ReflectionFunction($this->closure = Closure::fromCallable($this->callback));
			$this->name = $name;
		}

		/**
		 * @inheritdoc
		 */
		public function canHandle(IContainer $container, string $dependency): bool {
			if ($this->name === null) {
				$this->name = (string)$this->reflectionFunction->getReturnType();
			}
			return $dependency === $this->name;
		}

		/**
		 * @inheritdoc
		 */
		public function getReflection(IContainer $container, string $dependency = null): IReflection {
			$parameterList = [];
			foreach ($this->reflectionFunction->getParameters() as $reflectionParameter) {
				if (($parameterReflectionClass = $reflectionParameter->getClass()) === null) {
					if ($reflectionParameter->isOptional()) {
						break;
					}
					throw new ReflectionException(sprintf('Function [%s] parameter [%s] has missing class type hint or it is a scalar type.', $this->reflectionFunction->getName(), $reflectionParameter->getName()));
				}
				$parameterList[] = new Parameter($reflectionParameter->getName(), $reflectionParameter->isOptional(), $parameterReflectionClass->getName());
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

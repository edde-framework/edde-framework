<?php
	declare(strict_types = 1);

	namespace Edde\Common\Reflection;

	use Closure;
	use Edde\Api\Callback\ICallback;
	use Edde\Api\Callback\IParameter;
	use Edde\Api\Reflection\ReflectionException;
	use Edde\Common\AbstractObject;
	use Edde\Common\Callback\Parameter;
	use ReflectionClass;
	use ReflectionFunction;
	use ReflectionMethod;

	/**
	 * Set of tools for simplier reflection manipulation.
	 */
	class ReflectionUtils extends AbstractObject {
		/**
		 * bypass property visibility and set a given value
		 *
		 * @param $object
		 * @param $property
		 * @param $value
		 *
		 * @throws ReflectionException
		 */
		static public function setProperty($object, string $property, $value) {
			try {
				$reflectionClass = new \ReflectionClass($object);
				$reflectionProperty = $reflectionClass->getProperty($property);
				$reflectionProperty->setAccessible(true);
				$reflectionProperty->setValue($object, $value);
			} catch (\ReflectionException $exception) {
				throw new ReflectionException(sprintf('Property [%s::$%s] does not exists.', get_class($object), $property));
			}
		}

		/**
		 * bypass visibility and reads the given property of the given object
		 *
		 * @param $object
		 * @param $property
		 *
		 * @return mixed
		 * @throws ReflectionException
		 */
		static public function getProperty($object, string $property) {
			try {
				$reflectionClass = new \ReflectionClass($object);
				$reflectionProperty = $reflectionClass->getProperty($property);
				$reflectionProperty->setAccessible(true);
				return $reflectionProperty->getValue($object);
			} catch (\ReflectionException $exception) {
				throw new ReflectionException(sprintf('Property [%s::$%s] does not exists.', get_class($object), $property));
			}
		}

		/**
		 * @param string|array|callable $callback
		 *
		 * @return ReflectionFunction|ReflectionMethod
		 */
		public static function getMethodReflection($callback) {
			if (is_string($callback) && class_exists($callback)) {
				$reflectionClass = new ReflectionClass($callback);
				$callback = $reflectionClass->hasMethod('__construct') ? [
					$callback,
					'__construct',
				] : function () use ($reflectionClass) {
					return $reflectionClass->newInstance();
				};
			} else if ($callback instanceof Closure) {
				$reflectionFunction = new ReflectionFunction($callback);
				if (substr($reflectionFunction->getName(), -1) === '}') {
					$vars = $reflectionFunction->getStaticVariables();
					$callback = $vars['_callable_'] ?? $callback;
				} else if ($obj = $reflectionFunction->getClosureThis()) {
					$callback = [
						$obj,
						$reflectionFunction->getName(),
					];
				} else if ($class = $reflectionFunction->getClosureScopeClass()) {
					$callback = [
						$class->getName(),
						$reflectionFunction->getName(),
					];
				} else {
					$callback = $reflectionFunction->getName();
				}
			} else if ($callback instanceof ICallback) {
				$callback = $callback->getCallback();
			}
			$class = ReflectionMethod::class;
			if (is_string($callback) && strpos($callback, '::')) {
				return new $class($callback);
			} else if (is_array($callback)) {
				return new $class($callback[0], $callback[1]);
			} else if (is_object($callback) && ($callback instanceof Closure) === false) {
				return new $class($callback, '__invoke');
			}
			return new ReflectionFunction($callback);
		}

		/**
		 * @param callable|string $callback
		 *
		 * @return IParameter[]
		 */
		public static function getParameterList($callback): array {
			$reflection = ReflectionUtils::getMethodReflection($callback);
			$dependencyList = [];
			foreach ($reflection->getParameters() as $reflectionParameter) {
				$dependencyList[$reflectionParameter->getName()] = new Parameter($reflectionParameter->getName(), ($class = $reflectionParameter->getClass()) ? $class->getName() : null, $reflectionParameter->isOptional());
			}
			return $dependencyList;
		}
	}

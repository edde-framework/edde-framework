<?php
	declare(strict_types = 1);

	namespace Edde\Common\Reflection;

	use Closure;
	use Edde\Api\Callback\ICallback;
	use Edde\Api\Reflection\ReflectionException;
	use Edde\Common\AbstractObject;
	use ReflectionClass;
	use ReflectionFunction;
	use ReflectionMethod;

	/**
	 * Set of tools for simplier reflection manipulation.
	 */
	class ReflectionUtils extends AbstractObject {
		/**
		 * @var \ReflectionProperty[]|ReflectionClass[]|ReflectionMethod[]
		 */
		static protected $cache;

		static public function getReflectionClass($class): ReflectionClass {
			if (isset(self::$cache[$cacheId = 'class/' . (is_object($class) ? get_class($class) : $class)]) === false) {
				self::$cache[$cacheId] = new ReflectionClass($class);
			}
			return self::$cache[$cacheId];
		}

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
				if (isset(self::$cache[$cacheId = 'property/' . (is_object($object) ? get_class($object) : $object) . $property]) === false) {
					$reflectionClass = new ReflectionClass($object);
					self::$cache[$cacheId] = $reflectionProperty = $reflectionClass->getProperty($property);
					$reflectionProperty->setAccessible(true);
				}
				self::$cache[$cacheId]->setValue($object, $value);
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
				if (isset(self::$cache[$cacheId = 'property/' . (is_object($object) ? get_class($object) : $object) . $property]) === false) {
					$reflectionClass = new ReflectionClass($object);
					self::$cache[$cacheId] = $reflectionProperty = $reflectionClass->getProperty($property);
					$reflectionProperty->setAccessible(true);
				}
				return self::$cache[$cacheId]->getValue($object);
			} catch (\ReflectionException $exception) {
				throw new ReflectionException(sprintf('Property [%s::$%s] does not exists.', get_class($object), $property));
			}
		}

		/**
		 * @param string|array|callable $callback
		 *
		 * @return ReflectionFunction|ReflectionMethod
		 */
		static public function getMethodReflection($callback) {
			if (is_string($callback) && class_exists($callback)) {
				$reflectionClass = self::getReflectionClass($callback);
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
		 * @return \ReflectionParameter[]
		 */
		static public function getParameterList($callback): array {
			$parameterList = [];
			$reflection = ReflectionUtils::getMethodReflection($callback);
			foreach ($reflection->getParameters() as $reflectionParameter) {
				$parameterList[$reflectionParameter->getName()] = $reflectionParameter;
			}
			return $parameterList;
		}

		/**
		 * @param mixed $class
		 * @param int|null $filter
		 *
		 * @return ReflectionMethod[]
		 */
		static public function getMethodList($class, int $filter = null): array {
			if (isset(self::$cache[$cacheId = 'method-list/' . $filter . '/' . (is_object($class) ? get_class($class) : $class)]) === false) {
				$reflectionClass = self::getReflectionClass($class);
				self::$cache[$cacheId] = $filter ? $reflectionClass->getMethods($filter) : $reflectionClass->getMethods();
			}
			return self::$cache[$cacheId];
		}

		static public function getCode(callable $callback): string {
			$reflectionMethod = self::getMethodReflection($callback);
			$source = implode('', array_map('trim', array_slice(explode("\n", file_get_contents($reflectionMethod->getFileName())), $start = ($reflectionMethod->getStartLine() - 1), $reflectionMethod->getEndLine() - $start)));
			$index = 0;
			$start = null;
			$end = 0;
			$track = 0;
			foreach (token_get_all("<?php $source") as $token) {
				list($token, $fragment) = is_array($token) ? $token : [
					$token,
					$token,
				];
				switch ($token) {
					case T_FUNCTION:
						if ($start === null) {
							/** 6 because of fake opening php tag*/
							$start = $index - 6;
						}
						break;
					case '{':
						if ($start !== null) {
							$track++;
						}
						break;
					case '}':
						if ($start !== null && --$track <= 0) {
							$end = $index - 6;
							break 2;
						}
						break;
				}
				$index += strlen($fragment);
			}
			return substr($source, $start, $end - $start + 1);
		}
	}

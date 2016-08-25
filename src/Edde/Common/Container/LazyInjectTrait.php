<?php
	declare(strict_types = 1);

	namespace Edde\Common\Container;

	use Edde\Api\Container\ContainerException;
	use Edde\Api\Container\IContainer;

	trait LazyInjectTrait {
		protected $lazyInjectList = [];

		public function lazy(IContainer $container) {
			call_user_func(\Closure::bind(function (IContainer $container) {
				$reflectionClass = new \ReflectionClass($this);
				foreach ($reflectionClass->getMethods(\ReflectionMethod::IS_PUBLIC) as $reflectionMethod) {
					$name = $reflectionMethod->getName();
					if ($reflectionMethod->getNumberOfParameters() === 0 || strlen($name) <= 4 || strpos($name, 'lazy') === false) {
						continue;
					}
					foreach ($reflectionMethod->getParameters() as $reflectionParameter) {
						if ($reflectionClass->hasProperty($reflectionParameter->getName()) === false) {
							throw new ContainerException(sprintf('Cannot bind lazy method [%s::%s] - missing property [%s::%s].', $reflectionClass->getName(), $name, $reflectionClass->getName(), $reflectionParameter->getName()));
						}
						$propertyName = $reflectionParameter->getName();
						/** @noinspection PhpVariableVariableInspection */
						unset($this->$propertyName);
						$class = $reflectionParameter->getClass()
							->getName();
						$this->lazyInjectList[$propertyName] = function () use ($container, $class) {
							return $container->create($class);
						};
					}
				}
				foreach ($this->lazyList() as $property => $callback) {
					$this->lazyInjectList[$property] = $callback;
					/** @noinspection PhpVariableVariableInspection */
					unset($this->$property);
				}
			}, $this), $container);
		}

		public function __get($name) {
			if (($dependency = $this->getLazyDependency($name)) === false) {
				/** @noinspection PhpUndefinedClassInspection */
				/** @noinspection PhpUndefinedMethodInspection */
				return parent::__get($name);
			}
			return $dependency;
		}

		/** @noinspection MagicMethodsValidityInspection */
		public function __set($name, $value) {
			if ($this->setLazyDependency($name, $value) === false) {
				/** @noinspection PhpUndefinedClassInspection */
				/** @noinspection PhpUndefinedMethodInspection */
				return parent::__set($name, $value);
			}
			return $this;
		}

		public function getLazyDependency($name) {
			if (isset($this->lazyInjectList[$name]) === false) {
				return false;
			}
			/** @noinspection PhpVariableVariableInspection */
			return $this->$name = call_user_func($this->lazyInjectList[$name]);
		}

		public function setLazyDependency($name, $value) {
			if (isset($this->lazyInjectList[$name]) === false) {
				return false;
			}
			/** @noinspection PhpVariableVariableInspection */
			$this->$name = $value;
			return true;
		}

		protected function lazyList(): array {
			return [];
		}
	}

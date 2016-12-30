<?php
	declare(strict_types = 1);

	namespace Edde\Common;

	use Closure;
	use Edde\Api\Container\IContainer;
	use Edde\Api\Container\ILazyInject;
	use Edde\Api\EddeException;

	abstract class AbstractObject implements ILazyInject {
		protected $aInjectList = [];
		protected $aLazyaInjectList = [];

		protected function prepare() {
		}

		public function inject(string $property, $dependency) {
			$this->aInjectList[$property] = $dependency;
			$this->{$property} = $dependency;
			return $this;
		}

		public function lazy(string $property, IContainer $container, string $dependency, array $parameterList = []) {
			$this->aLazyaInjectList[$property] = [
				$container,
				$dependency,
				$parameterList,
			];
			call_user_func(Closure::bind(function (string $property) {
				/** @noinspection PhpVariableVariableInspection */
				unset($this->$property);
			}, $this, static::class), $property);
			return $this;
		}

		/**
		 * @param string $name
		 *
		 * @return mixed
		 * @throws EddeException
		 */
		public function __get(string $name) {
			if (isset($this->aLazyaInjectList[$name])) {
				/** @var $container IContainer */
				list($container, $dependency, $parameterList) = $this->aLazyaInjectList[$name];
				return $this->$name = $container->create($dependency, ...$parameterList);
			}
			throw new EddeException(sprintf('Reading from the undefined/private/protected property [%s::$%s].', static::class, $name));
		}

		/**
		 * @param string $name
		 * @param mixed  $value
		 *
		 * @return $this
		 * @throws EddeException
		 */
		public function __set(string $name, $value) {
			if (isset($this->aLazyaInjectList[$name])) {
				/** @noinspection PhpVariableVariableInspection */
				$this->$name = $value;
				return $this;
			}
			throw new EddeException(sprintf('Writing to the undefined/private/protected property [%s::$%s].', static::class, $name));
		}

		/**
		 * @param string $name
		 *
		 * @return bool
		 * @throws EddeException
		 */
		public function __isset(string $name) {
			if (isset($this->aLazyaInjectList[$name])) {
				return true;
			}
			throw new EddeException(sprintf('Cannot check isset on undefined/private/protected property [%s::$%s].', static::class, $name));
		}

		public function __wakeup() {
			foreach ($this->aInjectList as $property => $dependency) {
				if ($dependency !== null) {
					$this->{$property} = $dependency;
				}
			}
			foreach ($this->aLazyaInjectList as $property => $dependency) {
				if ($this->{$property} === null) {
					unset($this->{$property});
				}
			}
		}
	}

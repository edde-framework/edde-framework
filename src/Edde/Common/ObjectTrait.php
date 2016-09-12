<?php
	declare(strict_types = 1);

	namespace Edde\Common;

	use Edde\Api\EddeException;

	trait ObjectTrait {
		/**
		 * @var callable[]
		 */
		protected $objectPropertyList = [];

		public function lazy(string $property, callable  $callback) {
			return $this->objectProperty($property, $callback);
		}

		public function objectProperty(string $property, callable $callback) {
			$this->objectPropertyList[$property] = $callback;
			/**
			 * this magic allows to remove a private property, ou yay!
			 */
			call_user_func(\Closure::bind(function (string $property) {
				unset($this->$property);
			}, $this, static::class), $property);
			return $this;
		}

		public function __get($name) {
			if (isset($this->objectPropertyList[$name])) {
				return $this->$name = call_user_func($this->objectPropertyList[$name]);
			}
			throw new EddeException(sprintf('Reading from the undefined/private/protected property [%s::$%s].', static::class, $name));
		}

		public function __set($name, $value) {
			if (isset($this->objectPropertyList[$name])) {
				$this->$name = $value;
				return $this;
			}
			throw new EddeException(sprintf('Writing to the undefined/private/protected property [%s::$%s].', static::class, $name));
		}

		public function __isset($name) {
			if (isset($this->objectPropertyList[$name])) {
				return true;
			}
			throw new EddeException(sprintf('Cannot check isset on undefined/private/protected property [%s::$%s].', static::class, $name));
		}
	}

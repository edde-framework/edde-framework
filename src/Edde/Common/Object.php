<?php
	declare(strict_types = 1);

	namespace Edde\Common;

	use Closure;
	use Edde\Api\Cache\ICacheable;
	use Edde\Api\Container\IContainer;
	use Edde\Api\Container\ILazyInject;
	use Edde\Api\EddeException;

	/**
	 * While watching TV with his wife, a man tosses peanuts into the air and catches them in his mouth.
	 * Just as he throws another peanut into the air, the front door opens, causing him to turn his head.
	 * The peanut falls into his ear and gets stuck.
	 * His daughter comes in with her date.
	 * The man explains the situation, and the daughter's date says, "I can get the peanut out."
	 * He tells the father to sit down, shoves two fingers into the father's nose, and tells him to blow hard.
	 * The father blows, and the peanut flies out of his ear.
	 * After the daughter takes her date to the kitchen for something to eat, the mother turns to the father and says, "Isn't he smart? I wonder what he plans to be."
	 * The father says, "From the smell of his fingers, I'd say our son-in-law."
	 */
	class Object implements ILazyInject {
		protected $aId;
		protected $aInjectList = [];
		protected $aLazyInjectList = [];

		/**
		 * return object hash (unique id); object has is NOT based on internal state; ist's only
		 *
		 * @return string
		 */
		public function hash(): string {
			if ($this->aId === null) {
				$this->aId = hash('sha512', spl_object_hash($this));
			}
			return $this->aId;
		}

		protected function prepare() {
		}

		public function inject(string $property, $dependency) {
			$this->aInjectList[$property] = $dependency;
			$this->{$property} = $dependency;
			return $this;
		}

		public function lazy(string $property, IContainer $container, string $dependency, array $parameterList = []) {
			$this->aLazyInjectList[$property] = [
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
			if (isset($this->aLazyInjectList[$name])) {
				/** @var $container IContainer */
				list($container, $dependency, $parameterList) = $this->aLazyInjectList[$name];
				return $this->$name = $container->create($dependency, $parameterList, static::class);
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
			if (isset($this->aLazyInjectList[$name])) {
				/** @noinspection PhpVariableVariableInspection */
				$this->$name = $value;
				return $this;
			}
			throw new EddeException(sprintf('Writing to the undefined/private/protected property [%s::$%s].', static::class, $name));
		}

		public function __sleep() {
			static $allowed = [
				\stdClass::class,
				\SplStack::class,
			];
			$reflectionClass = new \ReflectionClass($this);
			foreach ($reflectionClass->getProperties() as $reflectionProperty) {
				$name = $reflectionProperty->getName();
				if (isset($this->{$name}) === false) {
					continue;
				} else if (strpos($doc = is_string($doc = $reflectionProperty->getDocComment()) ? $doc : '', '@no-cache') !== false) {
					unset($this->{$name});
				} else if (is_object($this->{$name}) && $this->{$name} instanceof ICacheable === false && in_array($class = get_class($this->{$name}), $allowed) === false) {
					if (strpos($doc, '@cache-optional') === false) {
						throw new EddeException(sprintf('Trying to serialize object [%s] which is not cacheable.', $class));
					}
					unset($this->{$name});
				}
			}
			return array_keys(get_object_vars($this));
		}

		public function __wakeup() {
			foreach ($this->aInjectList as $property => $dependency) {
				if ($dependency !== null) {
					$this->{$property} = $dependency;
				}
			}
			foreach ($this->aLazyInjectList as $property => $dependency) {
				if ($this->{$property} === null) {
					unset($this->{$property});
				}
			}
		}
	}

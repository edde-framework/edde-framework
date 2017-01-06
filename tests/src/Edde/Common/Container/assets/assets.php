<?php
	declare(strict_types = 1);

	use Edde\Api\Cache\ICacheable;
	use Edde\Api\Container\IConfigHandler;
	use Edde\Api\Container\IConfigurable;
	use Edde\Api\Container\LazyContainerTrait;
	use Edde\Common\Cache\CacheableTrait;
	use Edde\Common\Container\AbstractConfigHandler;
	use Edde\Common\Container\ConfigurableTrait;
	use Edde\Common\Object;

	interface ISomething {
		public function registerSomeething(string $something);
	}

	class FirstSomethingSetup extends AbstractConfigHandler implements IConfigHandler, ICacheable {
		/**
		 * @param ISomething $instance
		 */
		public function config($instance) {
			$instance->registerSomeething('foo');
		}

		public function getBoo() {
			return 'boo';
		}
	}

	class AnotherSomethingSetup extends AbstractConfigHandler implements IConfigHandler, ICacheable {
		use LazyContainerTrait;

		/**
		 * @param ISomething $instance
		 */
		public function config($instance) {
			$instance->registerSomeething('bar');
			$instance->registerSomeething($this->container->create(FirstSomethingSetup::class)
				->getBoo());
		}
	}

	class Something extends Object implements ISomething, IConfigurable, ICacheable {
		use ConfigurableTrait;
		use CacheableTrait;

		public $someParameter;
		public $anotherSomething;
		public $injectedSomething;
		public $lazySomething;
		public $anotherAnotherSomething;
		public $somethingList = [];

		public function __construct($someParameter, AnotherSomething $anotherSomething) {
			$this->someParameter = $someParameter;
			$this->anotherSomething = $anotherSomething;
		}

		public function injectSomething(InjectedSomething $injectedSomething) {
		}

		public function lazySomething(LazySomething $lazySomething, AnotherAnotherSomething $anotherAnotherSomething) {
		}

		public function registerSomeething(string $something) {
			$this->somethingList[] = $something;
			return $this;
		}
	}

	class AnotherSomething extends Object implements IConfigurable, ICacheable {
		use ConfigurableTrait;
	}

	class InjectedSomething extends Object implements ICacheable {
	}

	class LazySomething extends Object implements ICacheable {
	}

	class AnotherAnotherSomething extends Object implements ICacheable {
	}

	class ThisIsCleverManager extends Object implements ICacheable {
		/**
		 * @var AnotherSomething
		 */
		protected $anotherSomething;
		/**
		 * @var InjectedSomething
		 */
		protected $injectedSomething;

		/**
		 * @param AnotherSomething  $anotherSomething
		 * @param InjectedSomething $injectedSomething
		 */
		public function __construct(AnotherSomething $anotherSomething, InjectedSomething $injectedSomething) {
			$this->anotherSomething = $anotherSomething;
			$this->injectedSomething = $injectedSomething;
		}

		public function createCleverProduct(): ThisIsProductOfCleverManager {
			return new ThisIsProductOfCleverManager();
		}
	}

	class ThisIsProductOfCleverManager extends Object {
	}

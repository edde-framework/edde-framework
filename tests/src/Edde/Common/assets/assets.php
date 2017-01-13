<?php
	declare(strict_types = 1);

	namespace Edde\Test;

	use Edde\Api\Cache\ICacheable;
	use Edde\Common\Object;

	class FooObject extends Object implements ICacheable {
		public $foo = 'bar';
	}

	class BarObject extends Object implements ICacheable {
		public $bar = 'foo';
		/**
		 * @var FooObject
		 */
		protected $foo;

		/**
		 * @param FooObject $foo
		 */
		public function __construct(FooObject $foo) {
			$this->foo = $foo;
		}

		public function getFoo(): FooObject {
			return $this->foo;
		}
	}

	class FooNotCacheable extends Object {
	}

	class CompositeObject extends Object implements ICacheable {
		/**
		 * @var FooObject
		 */
		protected $foo;
		/**
		 * @var BarObject
		 */
		protected $bar;

		/**
		 * @param FooObject $foo
		 * @param BarObject $bar
		 */
		public function __construct(FooObject $foo, BarObject $bar) {
			$this->foo = $foo;
			$this->bar = $bar;
		}

		public function getFoo(): FooObject {
			return $this->foo;
		}

		public function getBar(): BarObject {
			return $this->bar;
		}
	}

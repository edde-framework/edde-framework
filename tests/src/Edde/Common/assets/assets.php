<?php
	declare(strict_types = 1);

	namespace Edde\Test;

	use Edde\Api\Serialize\IHashable;
	use Edde\Common\Serialize\AbstractSerializable;

	class FooObject extends AbstractSerializable implements IHashable {
		public $foo = 'bar';
	}

	class BarObject extends AbstractSerializable implements IHashable {
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

	class CompositeObject extends AbstractSerializable implements IHashable {
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

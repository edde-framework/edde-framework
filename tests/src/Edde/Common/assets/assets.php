<?php
	declare(strict_types=1);

	namespace Edde\Test;

	use Edde\Common\Object\Object;

	class FooObject extends Object {
		public $foo = 'foo';
	}

	class BarObject extends Object {
		public $bar = 'bar';
		public $fooObject;

		public function __construct(FooObject $fooObject) {
			$this->fooObject = $fooObject;
		}
	}

	class FooBarObject extends Object {
		public $fooObject;
		public $barObject;

		public function __construct(FooObject $fooObject, BarObject $barObject) {
			$this->fooObject = $fooObject;
			$this->barObject = $barObject;
		}
	}

	class ConstructorDependencyObject extends Object {
		public $fooObject;
		public $barObject;

		public function __construct(FooObject $fooObject, BarObject $barObject) {
			$this->fooObject = $fooObject;
			$this->barObject = $barObject;
		}
	}

	class InjectDependencyObject extends Object {
		public $fooObject;
		public $barObject;

		public function injectFooObject(FooObject $fooObject) {
			$this->fooObject = $fooObject;
		}

		public function injectBarObject(BarObject $barObject) {
			$this->barObject = $barObject;
		}
	}

	class AutowireDependencyObject extends Object {
		public $fooObject;
		public $barObject;

		public function lazyFooObject(FooObject $fooObject) {
			$this->fooObject = $fooObject;
		}

		public function lazyBarObject(BarObject $barObject) {
			$this->barObject = $barObject;
		}
	}

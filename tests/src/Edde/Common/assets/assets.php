<?php
	declare(strict_types=1);

	namespace Edde\Test;

	use Edde\Common\Object\Object;

	class FooObject extends Object {
	}

	class BarObject extends Object {
		public $fooObjecct;

		public function __construct(FooObject $fooObjecct) {
			$this->fooObjecct = $fooObjecct;
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

<?php
	declare(strict_types=1);

	namespace Edde\Test;

	use Edde\Api\Protocol\Request\IMessage;
	use Edde\Common\Object;
	use Edde\Common\Protocol\Request\Response;

	class FooObject extends Object {
		public $foo = 'bar';
	}

	class BarObject extends Object {
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

	class CompositeObject extends Object {
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

	class ExecutableService extends Object {
		public function noResponse() {
		}

		public function method(IMessage $request) {
			return (new Response())->put(['data' => $request->get('foo')]);
		}
	}

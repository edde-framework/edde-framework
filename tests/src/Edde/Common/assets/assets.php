<?php
	declare(strict_types=1);

	namespace Edde\Test;

	use Edde\Api\Protocol\IElement;
	use Edde\Api\Protocol\Request\IMessage;
	use Edde\Api\Protocol\Request\IRequest;
	use Edde\Common\Object;
	use Edde\Common\Protocol\Request\AbstractRequestHandler;
	use Edde\Common\Protocol\Request\Response;
	use Edde\Ext\Protocol\RequestServiceConfigurator;

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

	class TestRequestHandler extends AbstractRequestHandler {
		/**
		 * @param IRequest|IMessage $element
		 */
		protected function element(IElement $element) {
			if ($element->getRequest() === 'testquest') {
				return (new Response('foobar'))->put(['a' => 'b']);
			}
			return null;
		}
	}

	class TestRequestServiceConfigurator extends RequestServiceConfigurator {
		public function configure($instance) {
			parent::configure($instance);
			$instance->registerRequestHandler($this->container->create(TestRequestHandler::class));
		}
	}

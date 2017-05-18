<?php
	declare(strict_types=1);

	namespace Edde\Test;

	use Edde\Api\Protocol\IElement;
	use Edde\Common\Application\AbstractContext;
	use Edde\Common\Object;
	use Edde\Common\Protocol\Request\AbstractRequestHandler;
	use Edde\Common\Protocol\Request\Response;
	use Edde\Common\Session\AbstractFingerprint;
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

		public function method(IElement $request) {
			return (new Response())->data(['got-this' => $request->getMeta('foo')]);
		}

		public function doThis(IElement $element) {
			return $this->method($element);
		}
	}

	class TestRequestHandler extends AbstractRequestHandler {
		/**
		 * @inheritdoc
		 */
		public function canHandle(IElement $element): bool {
			return $element->getAttribute('request') === 'testquest';
		}

		/**
		 * @inheritdoc
		 */
		public function execute(IElement $element) {
			return (new Response('foobar'))->data(['a' => 'b']);
		}
	}

	class TestRequestServiceConfigurator extends RequestServiceConfigurator {
		public function configure($instance) {
			parent::configure($instance);
			$instance->registerRequestHandler($this->container->create(TestRequestHandler::class));
		}
	}

	class TestyFingerprint extends AbstractFingerprint {
		public function fingerprint() {
			return 'boo';
		}
	}

	class TestContext extends AbstractContext {
		/**
		 * @inheritdoc
		 */
		public function cascade(string $delimiter, string $name = null): array {
			return [
				'Edde' . $delimiter . $name,
				'Edde' . $delimiter . 'Test' . ($name ? $delimiter . $name : ''),
			];
		}
	}

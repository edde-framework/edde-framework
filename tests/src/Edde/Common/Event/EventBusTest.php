<?php
	declare(strict_types = 1);

	namespace Edde\Common\Event;

	use Edde\Api\Event\EventException;
	use Edde\Api\Event\IEventBus;
	use Edde\Common\Event\Handler\CallableHandler;
	use Edde\Common\Event\Handler\ReflectionHandler;
	use Foo\Bar\AnotherEvent;
	use Foo\Bar\DummyEvent;
	use Foo\Bar\EventHandler;
	use Foo\Bar\MultiEventHandler;
	use Foo\Bar\SomeEvent;
	use Foo\Bar\SomeUsefullClass;
	use phpunit\framework\TestCase;

	require_once(__DIR__ . '/assets/assets.php');

	class EventBusTest extends TestCase {
		/**
		 * @var IEventBus
		 */
		protected $eventBus;

		public function testCommon() {
			$this->eventBus->register(SomeEvent::class, function (SomeEvent $someEvent) {
				$someEvent->flag = true;
			});
			$this->eventBus->event($someEvent = new SomeEvent());
			self::assertTrue($someEvent->flag);
		}

		public function testHandler() {
			$this->eventBus->handler(new ReflectionHandler(new EventHandler()));
			$this->eventBus->event($event = new SomeEvent());
			self::assertTrue($event->flag);
			$this->eventBus->event($event = new AnotherEvent());
			self::assertTrue($event->flag);
			$this->eventBus->event($event = new DummyEvent());
			self::assertFalse($event->flag);
		}

		public function testMultiHandlerError() {
			$this->expectException(EventException::class);
			$this->expectExceptionMessage('Event class [Foo\Bar\SomeEvent] was already registered in handler [Foo\Bar\MultiEventHandler].');
			$this->eventBus->handler(new ReflectionHandler(new MultiEventHandler()));
			$this->eventBus->event($event = new SomeEvent());
		}

		public function testTraitBusHandler() {
			$someUsefullClass = new SomeUsefullClass();
			$someUsefullClass->handler(new ReflectionHandler(new EventHandler()));
			$someUsefullClass->event($event = new SomeEvent());
			self::assertTrue($event->flag);
			$someUsefullClass->event($event = new AnotherEvent());
			self::assertTrue($event->flag);
			$someUsefullClass->event($event = new DummyEvent());
			self::assertFalse($event->flag);
		}

		public function testCallableHandler() {
			$flag = false;
			$this->eventBus->handler(new CallableHandler(function (DummyEvent $dummyEvent) use (&$flag) {
				$flag = true;
			}));
			self::assertFalse($flag);
			$this->eventBus->event(new DummyEvent());
			self::assertTrue($flag);
		}

		public function testCallableHandler2() {
			$flag = false;
			/**
			 * only for "event bus activation" (->use() is not formally available)
			 */
			$this->eventBus->event(new DummyEvent());
			$this->eventBus->listen(function (DummyEvent $dummyEvent) use (&$flag) {
				$flag = true;
			});
			self::assertFalse($flag);
			$this->eventBus->event(new DummyEvent());
			self::assertTrue($flag, 'Event was not called.');
		}

		protected function setUp() {
			$this->eventBus = new EventBus();
		}
	}

<?php
	declare(strict_types = 1);

	namespace Edde\Common\Event;

	use Edde\Api\Event\EventException;
	use Edde\Api\Event\IEventBus;
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
			$this->eventBus->listen(SomeEvent::class, function (SomeEvent $someEvent) {
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

		protected function setUp() {
			$this->eventBus = new EventBus();
		}
	}

<?php
	declare(strict_types = 1);

	namespace Foo\Bar;

	use Edde\Api\Event\IEventBus;
	use Edde\Common\Event\AbstractEvent;
	use Edde\Common\Event\EventTrait;

	class SomeEvent extends AbstractEvent {
		public $flag = false;
	}

	class AnotherEvent extends AbstractEvent {
		public $flag = false;
	}

	class DummyEvent extends AbstractEvent {
		public $flag = false;
	}

	class EventHandler {
		public function someEvent(SomeEvent $someEvent) {
			$someEvent->flag = true;
		}

		public function anotherEvent(AnotherEvent $anotherEvent) {
			$anotherEvent->flag = true;
		}
	}

	class MultiEventHandler extends EventHandler {
		public function someSomeEvent(SomeEvent $someEvent) {
			$someEvent->flag = true;
		}
	}

	class SomeUsefullClass implements IEventBus {
		use EventTrait;

		public function __construct(IEventBus $eventBus) {
			$this->eventBus = $eventBus;
		}
	}

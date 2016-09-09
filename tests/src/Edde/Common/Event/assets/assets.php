<?php
	declare(strict_types = 1);

	namespace Foo\Bar;

	use Edde\Common\Event\AbstractEvent;

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

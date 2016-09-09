<?php
	declare(strict_types = 1);

	namespace Edde\Common\Event;

	use phpunit\framework\TestCase;

	require_once(__DIR__ . '/assets/assets.php');

	class EventBusTest extends TestCase {
		public function testCommon() {
			$eventBus = new EventBus();
			$eventBus->listen(\SomeEvent::class, function (\SomeEvent $someEvent) {
				$someEvent->flag = true;
			});
			$eventBus->event($someEvent = new \SomeEvent());
			self::assertTrue($someEvent->flag);
		}
	}

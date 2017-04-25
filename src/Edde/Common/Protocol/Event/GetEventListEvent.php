<?php
	declare(strict_types=1);

	namespace Edde\Common\Protocol\Event;

	use Edde\Api\Protocol\IEvent;
	use Edde\Common\Protocol\Event;

	class GetEventListEvent extends Event {
		/**
		 * @var IEvent[]
		 */
		protected $eventList = [];

		public function addEvent(IEvent $event): GetEventListEvent {
			$this->eventList[] = $event;
			return $this;
		}

		public function getEventList(): array {
			return $this->eventList;
		}
	}

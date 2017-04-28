<?php
	declare(strict_types=1);

	namespace Edde\Common\Protocol\Event;

	use Edde\Api\Protocol\Event\IEvent;

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

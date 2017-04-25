<?php
	declare(strict_types=1);

	namespace Edde\Common\Protocol\Event;

	use Edde\Api\Protocol\IEvent;
	use Edde\Common\Protocol\Event;

	class GetEventListEvent extends Event {
		/**
		 * @var string
		 */
		protected $scope;
		/**
		 * @var string[]
		 */
		protected $tagList;
		/**
		 * @var IEvent[]
		 */
		protected $eventList = [];

		public function setScope(string $scope = null): GetEventListEvent {
			$this->scope = $scope;
			return $this;
		}

		public function setTagList(array $tagList = null): GetEventListEvent {
			$this->tagList = $tagList;
			return $this;
		}

		public function addEvent(IEvent $event): GetEventListEvent {
			$this->eventList[] = $event;
			return $this;
		}

		public function getEventList(): array {
			return $this->eventList;
		}
	}

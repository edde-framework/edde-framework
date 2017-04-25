<?php
	declare(strict_types=1);

	namespace Edde\Common\Protocol;

	use Edde\Api\Protocol\IEvent;
	use Edde\Api\Protocol\IEventBus;
	use Edde\Common\Config\ConfigurableTrait;
	use Edde\Common\Object;
	use Edde\Common\Protocol\Event\GetEventListEvent;

	class EventBus extends Object implements IEventBus {
		use ConfigurableTrait;

		/**
		 * @inheritdoc
		 */
		public function update(string $scope = null, array $tagList = null): IEventBus {
			return $this;
		}

		/**
		 * @inheritdoc
		 */
		public function getEventList(string $scope = null, array $tagList = null): array {
			$event = (new GetEventListEvent())->setScope($scope)
				->setTagList($tagList);
			$this->emit($event);
			if ($event->isCanceled()) {
				return [];
			}
			return $event->getEventList();
		}

		/**
		 * @inheritdoc
		 */
		public function emit(IEvent $event): IEventBus {
			return $this;
		}

		/**
		 * @inheritdoc
		 */
		public function queue(IEvent $event): IEventBus {
			return $this;
		}
	}

<?php
	declare(strict_types=1);

	namespace Edde\Common\Protocol\Event;

	use Edde\Api\Protocol\Event\IEvent;
	use Edde\Api\Protocol\Event\IEventBus;
	use Edde\Api\Protocol\Event\IListener;
	use Edde\Api\Protocol\IElement;
	use Edde\Common\Protocol\AbstractProtocolHandler;

	class EventBus extends AbstractProtocolHandler implements IEventBus {
		/**
		 * @var callable[]
		 */
		protected $callbackList = [];

		/**
		 * @inheritdoc
		 */
		public function register(IListener $listener): IEventBus {
			foreach ($listener->getListenerList() as $event => $listener) {
				$this->listen($event, $listener);
			}
			return $this;
		}

		/**
		 * @inheritdoc
		 */
		public function listen(string $event, callable $callback): IEventBus {
			$this->callbackList[$event][] = $callback;
			return $this;
		}

		/**
		 * @inheritdoc
		 */
		public function getEventList(string $scope = null, array $tagList = null): array {
			$event = new GetEventListEvent();
			$event->setScope($scope);
			$event->setTagList($tagList);
			$this->emit($event);
			if ($event->isCanceled()) {
				return [];
			}
			return $event->getEventList();
		}

		/**
		 * @inheritdoc
		 */
		public function canHandle(IElement $element): bool {
			return $element->getType() === 'event' && $element instanceof IEvent;
		}

		/**
		 * @inheritdoc
		 *
		 * @param IEvent $element
		 */
		public function execute(IElement $element) {
			if (isset($this->callbackList[$type = $element->getEvent()])) {
				foreach ($this->callbackList[$type] as $callback) {
					$callback($element);
				}
			}
			return $this;
		}

		/**
		 * @inheritdoc
		 */
		public function emit(IEvent $event): IEventBus {
			$this->element($event);
			return $this;
		}
	}

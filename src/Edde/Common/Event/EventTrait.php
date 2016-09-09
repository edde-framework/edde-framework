<?php
	declare(strict_types = 1);

	namespace Edde\Common\Event;

	use Edde\Api\Event\EventException;
	use Edde\Api\Event\IEvent;
	use Edde\Api\Event\IEventBus;

	trait EventTrait {
		/**
		 * event bus must be provided from "outside" to prevent magical appearance
		 *
		 * @var IEventBus
		 */
		protected $eventBus;

		public function listen(string $event, callable $handler): IEventBus {
			if ($this->eventBus === null) {
				throw new EventException(sprintf('An instance of [%s] was not provided to the class [%s] of trait [%s::$eventBus].', IEventBus::class, static::class, EventTrait::class));
			}
			return $this->eventBus->listen($event, $handler);
		}

		public function handler($handler): IEventBus {
			if ($this->eventBus === null) {
				throw new EventException(sprintf('An instance of [%s] was not provided to the class [%s] of trait [%s::$eventBus].', IEventBus::class, static::class, EventTrait::class));
			}
			return $this->eventBus->handler($handler);
		}

		public function event(IEvent $event): IEventBus {
			if ($this->eventBus === null) {
				throw new EventException(sprintf('An instance of [%s] was not provided to the class [%s] of trait [%s::$eventBus].', IEventBus::class, static::class, EventTrait::class));
			}
			return $this->eventBus->event($event);
		}
	}

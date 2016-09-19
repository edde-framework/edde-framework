<?php
	declare(strict_types = 1);

	namespace Edde\Common\Event;

	use Edde\Api\Event\IEvent;
	use Edde\Api\Event\IEventBus;
	use Edde\Api\Event\IHandler;

	trait EventTrait {
		/**
		 * local event bus from a EventTrait
		 *
		 * @var IEventBus
		 */
		protected $traitEventBus;

		public function handler(IHandler $handler): IEventBus {
			if ($this->traitEventBus === null) {
				$this->traitEventBus = new EventBus();
			}
			return $this->traitEventBus->handler($handler);
		}

		public function register($register): IEventBus {
			if ($this->traitEventBus === null) {
				$this->traitEventBus = new EventBus();
			}
			return $this->traitEventBus->register($register);
		}

		public function listen(string $event, callable $handler): IEventBus {
			if ($this->traitEventBus === null) {
				$this->traitEventBus = new EventBus();
			}
			return $this->traitEventBus->listen($event, $handler);
		}

		public function event(IEvent $event): IEventBus {
			if ($this->traitEventBus === null) {
				$this->traitEventBus = new EventBus();
			}
			return $this->traitEventBus->event($event);
		}
	}

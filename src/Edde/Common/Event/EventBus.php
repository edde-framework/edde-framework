<?php
	declare(strict_types = 1);

	namespace Edde\Common\Event;

	use Edde\Api\Event\IEvent;
	use Edde\Api\Event\IEventBus;
	use Edde\Common\AbstractObject;

	class EventBus extends AbstractObject implements IEventBus {
		/**
		 * @var callable[][]
		 */
		protected $listenList = [];

		public function listen(string $event, callable $handler): IEventBus {
			$this->listenList[$event][] = $handler;
			return $this;
		}

		public function event(IEvent $event): IEventBus {
			if (isset($this->listenList[$name = get_class($event)]) === false) {
				return $this;
			}
			foreach ($this->listenList[$name] as $callback) {
				$callback($event);
			}
			return $this;
		}
	}

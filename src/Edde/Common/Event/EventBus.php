<?php
	declare(strict_types = 1);

	namespace Edde\Common\Event;

	use Edde\Api\Event\IEvent;
	use Edde\Api\Event\IEventBus;
	use Edde\Api\Event\IHandler;
	use Edde\Common\Usable\AbstractUsable;
	use Edde\Common\Usable\UsableTrait;

	class EventBus extends AbstractUsable implements IEventBus {
		use UsableTrait;
		/**
		 * @var callable[][]
		 */
		protected $listenList = [];
		/**
		 * @var IHandler[]
		 */
		protected $handlerList = [];

		public function handler(IHandler $handler): IEventBus {
			if ($this->isUsed()) {
				$this->register($handler);
				return $this;
			}
			$this->handlerList[] = $handler;
			return $this;
		}

		public function register($register): IEventBus {
			if (($register instanceof IHandler) === false) {
				$register = HandlerFactory::handler($register);
			}
			foreach ($register as $event => $callable) {
				$this->listen($event, $callable);
			}
			return $this;
		}

		public function listen(string $event, callable $handler): IEventBus {
			$this->listenList[$event][] = $handler;
			return $this;
		}

		public function event(IEvent $event): IEventBus {
			$this->use();
			if (isset($this->listenList[$name = get_class($event)]) === false) {
				return $this;
			}
			foreach ($this->listenList[$name] as $callback) {
				$callback($event);
			}
			return $this;
		}

		protected function prepare() {
			foreach ($this->handlerList as $handler) {
				$this->register($handler);
			}
		}
	}

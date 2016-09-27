<?php
	declare(strict_types = 1);

	namespace Edde\Common\Event;

	use Edde\Api\Event\IEvent;
	use Edde\Api\Event\IEventBus;
	use Edde\Api\Event\IHandler;
	use Edde\Common\Deffered\AbstractDeffered;
	use Edde\Common\Deffered\DefferedTrait;

	/**
	 * Default simple implementation of an EventBus.
	 */
	class EventBus extends AbstractDeffered implements IEventBus {
		use DefferedTrait;
		/**
		 * @var callable[][]
		 */
		protected $listenList = [];
		/**
		 * @var IHandler[]
		 */
		protected $handlerList = [];

		/**
		 * @inheritdoc
		 */
		public function handler(IHandler $handler): IEventBus {
			if ($this->isUsed()) {
				$this->listen($handler);
				return $this;
			}
			$this->handlerList[] = $handler;
			return $this;
		}

		/**
		 * @inheritdoc
		 */
		public function listen($listen): IEventBus {
			if (($listen instanceof IHandler) === false) {
				$listen = HandlerFactory::handler($listen);
			}
			foreach ($listen as $event => $callable) {
				$this->register($event, $callable);
			}
			return $this;
		}

		/**
		 * @inheritdoc
		 */
		public function register(string $event, callable $handler): IEventBus {
			$this->listenList[$event][] = $handler;
			return $this;
		}

		/**
		 * @inheritdoc
		 */
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

		/**
		 * @inheritdoc
		 */
		protected function prepare() {
			foreach ($this->handlerList as $handler) {
				$this->listen($handler);
			}
		}
	}

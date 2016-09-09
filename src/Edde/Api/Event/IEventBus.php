<?php
	declare(strict_types = 1);

	namespace Edde\Api\Event;

	/**
	 * Simple (linear) event bus implementation.
	 */
	interface IEventBus {
		/**
		 * register event handler
		 *
		 * @param string $event
		 * @param callable $handler
		 *
		 * @return IEventBus
		 */
		public function listen(string $event, callable $handler): IEventBus;

		/**
		 * emit an event to all it's listeners; it should NOT do any magic
		 *
		 * @param IEvent $event
		 *
		 * @return IEventBus
		 */
		public function event(IEvent $event): IEventBus;
	}

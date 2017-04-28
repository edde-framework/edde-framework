<?php
	declare(strict_types=1);

	namespace Edde\Api\Protocol\Event;

	use Edde\Api\Config\IConfigurable;

	interface IEventBus extends IConfigurable {
		/**
		 * @param IListener $listener
		 *
		 * @return IEventBus
		 */
		public function register(IListener $listener): IEventBus;

		/**
		 * register listener for the given event
		 *
		 * @param string   $event
		 * @param callable $callback
		 *
		 * @return IEventBus
		 */
		public function listen(string $event, callable $callback): IEventBus;

		/**
		 * return event list by the given parameters
		 *
		 * @param string|null $scope
		 * @param array|null  $tagList
		 *
		 * @return IEvent[]
		 */
		public function getEventList(string $scope = null, array $tagList = null): array;

		/**
		 * immediately emmit the given event
		 *
		 * @param IEvent $event
		 *
		 * @return IEventBus
		 */
		public function emit(IEvent $event): IEventBus;

		/**
		 * enqueue the given event to be processed "later"
		 *
		 * @param IEvent $event
		 *
		 * @return IEventBus
		 */
		public function queue(IEvent $event): IEventBus;
	}

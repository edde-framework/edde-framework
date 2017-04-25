<?php
	declare(strict_types=1);

	namespace Edde\Api\Protocol;

	use Edde\Api\Config\IConfigurable;

	interface IEventBus extends IConfigurable {
		/**
		 * trigger event bus update
		 *
		 * @param string|null $scope
		 * @param array|null  $tagList
		 *
		 * @return IEventBus
		 */
		public function update(string $scope = null, array $tagList = null): IEventBus;

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

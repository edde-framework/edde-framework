<?php
	declare(strict_types=1);

	namespace Edde\Api\Protocol;

	interface IEvent extends IElement {
		/**
		 * get event type
		 *
		 * @return string
		 */
		public function getEvent(): string;

		/**
		 * inherit some values from the given event (scope, tag list, ...)
		 *
		 * @param IEvent $event
		 *
		 * @return IEvent
		 */
		public function inherit(IEvent $event): IEvent;

		/**
		 * cancel the current event; listener must respect this flag
		 *
		 * @param bool $cancel
		 *
		 * @return IEvent
		 */
		public function cancel(bool $cancel = true): IEvent;

		/**
		 * is an event canceled?
		 *
		 * @return bool
		 */
		public function isCanceled(): bool;
	}

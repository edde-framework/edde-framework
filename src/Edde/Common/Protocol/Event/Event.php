<?php
	declare(strict_types=1);

	namespace Edde\Common\Protocol\Event;

	use Edde\Api\Protocol\Event\IEvent;
	use Edde\Common\Protocol\Element;

	class Event extends Element implements IEvent {
		/**
		 * @var bool
		 */
		protected $cancel;

		public function __construct(string $event = null, string $id = null) {
			parent::__construct('event', $id);
			$this->setAttribute('event', $event ?: static::class);
			$this->cancel = false;
		}

		/**
		 * @inheritdoc
		 */
		public function getEvent(): string {
			return (string)$this->getAttribute('event');
		}

		/**
		 * @inheritdoc
		 */
		public function cancel(bool $cancel = true): IEvent {
			$this->cancel = $cancel;
			return $this;
		}

		/**
		 * @inheritdoc
		 */
		public function isCanceled(): bool {
			return $this->cancel;
		}
	}

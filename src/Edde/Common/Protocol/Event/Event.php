<?php
	declare(strict_types=1);

	namespace Edde\Common\Protocol\Event;

	use Edde\Api\Protocol\Event\IEvent;
	use Edde\Common\Protocol\AbstractElement;

	class Event extends AbstractElement implements IEvent {
		/**
		 * @var bool
		 */
		protected $cancel;

		public function __construct() {
			parent::__construct('event');
			$this->cancel = false;
		}

		/**
		 * @inheritdoc
		 */
		public function getEvent(): string {
			return static::class;
		}

		/**
		 * @inheritdoc
		 */
		public function inherit(IEvent $event): IEvent {
			$this->setScope($event->getScope());
			$this->setTagList(array_merge($this->getTagList(), $event->getTagList()));
			return $this;
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

		public function packet(): \stdClass {
			$packet = parent::packet();
			$packet->event = $this->getEvent();
			return $packet;
		}
	}

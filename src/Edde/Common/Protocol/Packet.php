<?php
	declare(strict_types=1);

	namespace Edde\Common\Protocol;

	use Edde\Api\Protocol\IElement;
	use Edde\Api\Protocol\IEvent;
	use Edde\Api\Protocol\IPacket;

	class Packet extends AbstractElement implements IPacket {
		/**
		 * @var string
		 */
		protected $version;
		/**
		 * @var IEvent[]
		 */
		protected $eventList = [];

		public function __construct($version = '1.0') {
			parent::__construct('packet');
			$this->version = $version;
		}

		/**
		 * @inheritdoc
		 */
		public function getVersion(): string {
			return $this->version;
		}

		/**
		 * @inheritdoc
		 */
		public function setElementList(array $elementList): IPacket {
			/** @var $element IElement */
			foreach ($elementList as $element) {
				switch ($element->getType()) {
					case 'event':
						$this->eventList[] = $element;
						break;
				}
			}
			return $this;
		}

		/**
		 * @inheritdoc
		 */
		public function setEventList(array $eventList = null): IPacket {
			if ($eventList === null) {
				$this->eventList = [];
				return $this;
			}
			$this->setElementList($eventList);
			return $this;
		}

		/**
		 * @inheritdoc
		 */
		public function packet(): \stdClass {
			$packet = parent::packet();
			$packet->version = $this->version;
			$elementList = $packet->elements = new \stdClass();
			if (empty($this->eventList) === false) {
				$elementList->event = [];
				foreach ($this->eventList as $event) {
					$elementList->event[] = $event->packet();
				}
			}
			return $packet;
		}

		public function __clone() {
			parent::__clone();
			$this->eventList = [];
		}
	}

<?php
	declare(strict_types=1);

	namespace Edde\Common\Protocol;

	use Edde\Api\Protocol\IElement;
	use Edde\Api\Protocol\IPacket;

	class Packet extends AbstractElement implements IPacket {
		/**
		 * @var string
		 */
		protected $version;
		/**
		 * @var IElement[]
		 */
		protected $elementList = [];

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
		public function addElement(IElement $element): IPacket {
			$this->elementList[] = $element;
			return $this;
		}

		/**
		 * @inheritdoc
		 */
		public function setElementList(array $elementList): IPacket {
			$this->elementList = [];
			/** @var $element IElement */
			foreach ($elementList as $element) {
				$this->addElement($element);
			}
			return $this;
		}

		/**
		 * @inheritdoc
		 */
		public function packet(): \stdClass {
			$packet = parent::packet();
			$packet->version = $this->version;
			if (empty($this->elementList) === false) {
				foreach ($this->elementList as $element) {
					$packet->elements[] = $element->packet();
				}
			}
			return $packet;
		}

		public function __clone() {
			parent::__clone();
			$this->elementList = [];
		}
	}

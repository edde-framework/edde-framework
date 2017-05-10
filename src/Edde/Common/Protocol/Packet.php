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
		protected $origin;
		/**
		 * @var IElement[]
		 */
		protected $elementList = [];
		/**
		 * @var IElement[]
		 */
		protected $referenceList = [];

		public function __construct() {
			parent::__construct('packet');
			$this->version = '1.0';
			$this->origin = '::the-void';
		}

		/**
		 * @inheritdoc
		 */
		public function setVersion(string $version): IPacket {
			$this->version = $version;
			return $this;
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
		public function setOrigin(string $origin): IPacket {
			$this->origin = $origin;
			return $this;
		}

		/**
		 * @inheritdoc
		 */
		public function getOrigin(): string {
			return $this->origin;
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
		public function getElementList(): array {
			return $this->elementList;
		}

		/**
		 * @inheritdoc
		 */
		public function addReference(IElement $element): IPacket {
			$this->referenceList[] = $element;
			return $this;
		}

		/**
		 * @inheritdoc
		 */
		public function setReferenceList(array $elementList): IPacket {
			$this->referenceList = [];
			foreach ($elementList as $element) {
				$this->addReference($element);
			}
			return $this;
		}

		/**
		 * @inheritdoc
		 */
		public function reference(IElement $reference): IElement {
			/** @var $element IElement */
			foreach (array_merge($this->referenceList, $this->elementList) as $element) {
				if ($element->isReferenceOf($reference)) {
					return $element;
				}
			}
			if ($this->isReferenceOf($reference)) {
				return $this;
			}
			throw new ReferenceException(sprintf('The element [%s (%s)] has no reference to itself by [%s].', get_class($reference), $reference->getType(), $reference->getId()));
		}

		/**
		 * @inheritdoc
		 */
		public function getReferenceList(): array {
			return $this->referenceList;
		}

		/**
		 * @inheritdoc
		 */
		public function from(\stdClass $from): IElement {
			parent::from($from);
			$this->version = $from->version;
			$this->origin = $from->origin;
			foreach ($from->elements ?? [] as $element) {
			}
			return $this;
		}

		/**
		 * @inheritdoc
		 */
		public function packet(): \stdClass {
			$packet = parent::packet();
			$packet->version = $this->version;
			$packet->origin = $this->origin;
			if ($this->reference) {
				$packet->reference = $this->reference->getId();
			}
			if (empty($this->elementList) === false) {
				foreach ($this->elementList as $element) {
					$packet->elements[] = $element->packet();
				}
			}
			if (empty($this->referenceList) === false) {
				foreach ($this->referenceList as $element) {
					$packet->references[] = $element->packet();
				}
			}
			return $packet;
		}

		public function __clone() {
			parent::__clone();
			$this->elementList = [];
		}
	}

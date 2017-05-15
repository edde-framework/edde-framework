<?php
	declare(strict_types=1);

	namespace Edde\Common\Protocol;

	use Edde\Api\Node\NodeException;
	use Edde\Api\Protocol\IElement;

	class Packet extends Element {
		public function __construct(string $origin) {
			parent::__construct('packet');
			$this->setAttribute('version', '1.1');
			$this->setAttribute('origin', $origin);
		}

		/**
		 * shortcut for add a new element to elements
		 *
		 * @param IElement $element
		 *
		 * @return Packet
		 * @throws NodeException
		 */
		public function element(IElement $element): Packet {
			$this->addElement('elements', $element);
			return $this;
		}

		/**
		 * shortuct to add a new element to references
		 *
		 * @param IElement $element
		 *
		 * @return Packet
		 * @throws NodeException
		 */
		public function reference(IElement $element): Packet {
			$this->addElement('references', $element);
			return $this;
		}
	}

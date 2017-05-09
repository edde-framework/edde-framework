<?php
	declare(strict_types=1);

	namespace Edde\Api\Protocol;

	interface IPacket extends IElement {
		/**
		 * @return string
		 */
		public function getVersion(): string;

		/**
		 * @param IElement $element
		 *
		 * @return IPacket
		 */
		public function addElement(IElement $element): IPacket;

		/**
		 * set element list which will be automagically aligned to proper properties of packet
		 *
		 * @param IElement[] $elementList
		 *
		 * @return IPacket
		 */
		public function setElementList(array $elementList): IPacket;

		/**
		 * @return IElement[]
		 */
		public function getElementList(): array;

		/**
		 * add an element as a reference
		 *
		 * @param IElement $element
		 *
		 * @return IPacket
		 */
		public function addReference(IElement $element): IPacket;

		/**
		 * @param IElement[] $elementList
		 *
		 * @return IPacket
		 */
		public function setReferenceList(array $elementList): IPacket;

		/**
		 * return reference by the given element or throw an exception if there is no referenced element
		 *
		 * @param IElement $reference
		 *
		 * @return IElement
		 */
		public function reference(IElement $reference): IElement;

		/**
		 * @return IElement[]
		 */
		public function getReferenceList(): array;
	}

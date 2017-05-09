<?php
	declare(strict_types=1);

	namespace Edde\Api\Protocol;

	interface IPacket extends IElement {
		/**
		 * set protocol version
		 *
		 * @param string $version
		 *
		 * @return IPacket
		 */
		public function setVersion(string $version): IPacket;

		/**
		 * @return string
		 */
		public function getVersion(): string;

		/**
		 * set the origin of the packet
		 *
		 * @param string $origin
		 *
		 * @return IPacket
		 */
		public function setOrigin(string $origin): IPacket;

		/**
		 * @return string
		 */
		public function getOrigin(): string;

		/**
		 * set async flag of the packet
		 *
		 * @param bool $async
		 *
		 * @return IPacket
		 */
		public function async(bool $async = true): IPacket;

		/**
		 * is the packet asynchonous
		 *
		 * @return bool
		 */
		public function isAsync(): bool;

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

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
	}

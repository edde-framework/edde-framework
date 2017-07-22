<?php
	declare(strict_types=1);

	namespace Edde\Api\Protocol;

	use Edde\Api\Config\IConfigurable;

	interface IProtocolManager extends IConfigurable {
		/**
		 * queue element to be included in packet created by self::createPacket();
		 * this method  is useful to collect elements around the world and then send
		 * them to somewhere (e.g. client)
		 *
		 * @param IElement $element
		 *
		 * @return IProtocolManager
		 */
		public function queue(IElement $element): IProtocolManager;

		/**
		 * @param IElement[] $elementList
		 *
		 * @return IProtocolManager
		 */
		public function queueList($elementList): IProtocolManager;

		/**
		 * create packet with payload of all available elements and references
		 *
		 * @param IElement|null $reference
		 *
		 * @return IPacket
		 */
		public function createPacket(IElement $reference = null): IPacket;

		/**
		 * execute the given element; if the element is async, it's moved to job queue
		 *
		 * @param IElement $element
		 *
		 * @return mixed
		 */
		public function execute(IElement $element): IElement;
	}

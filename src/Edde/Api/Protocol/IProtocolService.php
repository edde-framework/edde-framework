<?php
	declare(strict_types=1);

	namespace Edde\Api\Protocol;

	interface IProtocolService extends IProtocolHandler {
		/**
		 * @param IProtocolHandler $protocolHandler
		 *
		 * @return IProtocolService
		 */
		public function registerProtocolHandler(IProtocolHandler $protocolHandler): IProtocolService;

		/**
		 * @param IElement $element
		 *
		 * @return IProtocolHandler
		 */
		public function getProtocolHandler(IElement $element): IProtocolHandler;

		/**
		 * @return IProtocolHandler[]|\Iterator
		 */
		public function getProtocolHandleList();

		/**
		 * @return IProtocolService
		 */
		public function dequeue(): IProtocolService;

		/**
		 * create packet from enqueued elements
		 *
		 * @return IPacket
		 */
		public function createQueuePacket(): IPacket;

		/**
		 * just create a new packet
		 *
		 * @param string|null $origin
		 *
		 * @return IPacket
		 */
		public function createPacket(string $origin = null): IPacket;

		/**
		 * @param string $id
		 *
		 * @return IElement[]
		 */
		public function getReferenceList(string $id): array;
	}

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
		 * @param string     $scope
		 * @param array|null $tagList
		 *
		 * @return IElement[]|\Traversable
		 */
		public function getQueueList(string $scope, array $tagList = null);

		/**
		 * create packet from enqueued elements
		 *
		 * @param string     $scope
		 * @param array|null $tagList
		 *
		 * @return IPacket
		 */
		public function createQueuePacket(string $scope, array $tagList = null): IPacket;

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

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
		 * this method could be used as a factory method for Packet
		 *
		 * @param \stdClass|null $source
		 *
		 * @return IPacket
		 */
		public function createPacket(\stdClass $source = null): IPacket;
	}

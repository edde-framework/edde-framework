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
		 * execute the given Packet as a request and get a new Packet as a response
		 *
		 * @param IPacket $request
		 *
		 * @return IPacket
		 */
		public function request(IPacket $request): IPacket;
	}

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
	}

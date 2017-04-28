<?php
	declare(strict_types=1);

	namespace Edde\Api\Protocol;

	use Edde\Api\Config\IConfigurable;

	interface IProtocolService extends IProtocolHandler, IConfigurable {
		/**
		 * @param IProtocolHandler $protocolHandler
		 *
		 * @return IProtocolService
		 */
		public function registerProtocolHandler(IProtocolHandler $protocolHandler): IProtocolService;
	}

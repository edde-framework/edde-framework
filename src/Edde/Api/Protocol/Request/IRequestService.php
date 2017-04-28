<?php
	declare(strict_types=1);

	namespace Edde\Api\Protocol\Request;

	use Edde\Api\Protocol\IProtocolHandler;

	interface IRequestService extends IProtocolHandler {
		/**
		 * @param IRequestHandler $requestHandler
		 *
		 * @return IRequestService
		 */
		public function registerRequestHandler(IRequestHandler $requestHandler): IRequestService;
	}

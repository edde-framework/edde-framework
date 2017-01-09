<?php
	declare(strict_types = 1);

	namespace Edde\Api\Stream;

	interface IStreamClient extends IConnector {
		/**
		 * send data to the server
		 *
		 * @param string $buffer
		 *
		 * @return IStreamClient
		 */
		public function write(string $buffer): IStreamClient;
	}

<?php
	declare(strict_types = 1);

	namespace Edde\Api\Stream;

	interface IConnector {
		/**
		 * @return IConnector
		 */
		public function close(): IConnector;
	}

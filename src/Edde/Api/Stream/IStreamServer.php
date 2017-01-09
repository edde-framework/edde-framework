<?php
	declare(strict_types = 1);

	namespace Edde\Api\Stream;

	interface IStreamServer extends \IteratorAggregate {
		/**
		 * @param string $socket
		 *
		 * @return IStreamServer
		 */
		public function server(string $socket): IStreamServer;

		/**
		 * if the server has been switched to "offline" state, this run loop again
		 *
		 * @return IStreamServer
		 */
		public function online(): IStreamServer;

		/**
		 * ends server loop; server is not closed (so loop can be run again)
		 *
		 * @return IStreamServer
		 */
		public function offline(): IStreamServer;
	}

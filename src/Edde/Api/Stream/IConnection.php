<?php
	declare(strict_types = 1);

	namespace Edde\Api\Stream;

	interface IConnection {
		/**
		 * return connection stream; it should not be used for direct manipulation
		 *
		 * @return resource
		 */
		public function getStream();

		/**
		 * is connection still alive?
		 *
		 * @return bool
		 */
		public function isAlive(): bool;

		/**
		 * read the data from the stream
		 *
		 * @return IConnection
		 */
		public function read(): IConnection;

		/**
		 * close the connection
		 *
		 * @return IConnection
		 */
		public function close(): IConnection;

		/**
		 * write the data to the stream
		 *
		 * @param string $buffer
		 *
		 * @return IConnection
		 */
		public function write(string $buffer): IConnection;
	}

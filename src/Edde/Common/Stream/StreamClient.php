<?php
	declare(strict_types = 1);

	namespace Edde\Common\Stream;

	use Edde\Api\Stream\IConnection;
	use Edde\Api\Stream\IConnector;
	use Edde\Api\Stream\IStreamClient;
	use Edde\Api\Stream\StreamClientException;
	use Edde\Common\Object;

	class StreamClient extends Object implements IStreamClient {
		/**
		 * @var IConnection
		 */
		protected $connection;

		public function connect(string $socket): IStreamClient {
			if (($stream = stream_socket_client($socket)) === false) {
				throw new StreamClientException('Cannot open client socket');
			}
			stream_set_blocking($stream, false);
			$this->connection = new Connection($this, $stream, stream_socket_get_name($stream, false));
			return $this;
		}

		public function write(string $buffer): IStreamClient {
			$limit = 2048;
			$length = strlen($buffer);
			$count = 0;
			$read = $except = null;
			$connectionList = [$this->connection->getStream()];
			while ($count < $length) {
				if (stream_select($read, $connectionList, $except, 256000) > 0) {
					$count += fwrite(reset($connectionList), substr($buffer, $count, $limit), $limit);
				}
			}
			return $this;
		}

		public function close(): IConnector {
			$this->connection->close();
			$this->connection = null;
			return $this;
		}
	}

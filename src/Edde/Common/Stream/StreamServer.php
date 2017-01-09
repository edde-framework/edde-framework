<?php
	declare(strict_types = 1);

	namespace Edde\Common\Stream;

	use Edde\Api\Stream\IConnection;
	use Edde\Api\Stream\IStreamServer;
	use Edde\Api\Stream\StreamServerException;
	use Edde\Common\Object;

	class StreamServer extends Object implements IStreamServer {
		/**
		 * @var IConnection
		 */
		protected $connection;
		/**
		 * @var IConnection[]
		 */
		protected $connectionList;
		/**
		 * @var string
		 */
		protected $socket;
		/**
		 * @var bool
		 */
		protected $online;

		public function server(string $socket): IStreamServer {
			if (($stream = stream_socket_server($this->socket = $socket)) === false) {
				throw new StreamServerException('Cannot open server socket [%s].', $socket);
			}
			stream_set_blocking($stream, false);
			$this->connectionList[] = $this->connection = new Connection($this, $stream, stream_socket_get_name($stream, false));
			return $this->online();
		}

		public function online(): IStreamServer {
			$this->online = true;
			return $this;
		}

		public function isOnline(): bool {
			return $this->online === true;
		}

		public function offline(): IStreamServer {
			$this->online = false;
			return $this;
		}

		public function close() {
			$this->offline();
			usleep(50);
			foreach ($this->connectionList as $connection) {
				$connection->close();
			}
			$this->connectionList = $this->connection = null;
		}

		public function tick() {
			$connectionList = $writeList = $exceptList = $readList = array_map(function (IConnection $connection) {
				return $connection->getStream();
			}, $this->connectionList);
			if (($select = stream_select($readList, $writeList, $exceptList, 3)) === false) {
				throw new StreamServerException('Stream select has failed.');
			} else if ($select === 0) {
				/**
				 * nothing happened
				 */
				return $this->isOnline();
			}
			if (($index = array_search($this->connection->getStream(), $readList, true)) !== false) {
				unset($readList[$index]);
				if (($handle = stream_socket_accept($this->connection->getStream())) !== false) {
					stream_set_blocking($handle, false);
					$this->connectionList[] = $connection = new Connection($this, $handle, stream_socket_get_name($handle, true));
					$connection->hello();
				}
			}
			foreach ($readList as $stream) {
				$connection = $this->connectionList[$index = array_search($stream, $connectionList, true)];
				/**
				 * stream closed
				 */
				if (feof($stream)) {
					$connection->close();
					unset($this->connectionList[$index]);
					continue;
				}
				$connection->read();
			}
			return $this->isOnline();
		}
	}

<?php
	declare(strict_types = 1);

	namespace Edde\Common\Stream;

	use Edde\Api\Stream\IStreamServer;
	use Edde\Api\Stream\StreamServerException;
	use Edde\Common\Object;

	class StreamServer extends Object implements IStreamServer {
		/**
		 * @var resource
		 */
		protected $stream;
		/**
		 * @var string
		 */
		protected $socket;
		/**
		 * @var bool
		 */
		protected $online;

		public function server(string $socket): IStreamServer {
			if (($this->stream = stream_socket_server($this->socket = $socket)) === false) {
				throw new StreamServerException('Cannot open server socket [%s].', $socket);
			}
			return $this->online();
		}

		public function online(): IStreamServer {
			$this->online = true;
			return $this;
		}

		public function offline(): IStreamServer {
			$this->online = false;
			return $this;
		}

		public function close() {
			usleep(50);
			fflush($this->stream);
			stream_socket_shutdown($this->stream, STREAM_SHUT_RDWR);
			fclose($this->stream);
			$this->stream = null;
		}

		public function tick() {
			return false;
		}

		/**
		 * main loop of the server
		 */
		public function getIterator() {
			while ($this->online) {
				if (($tick = $this->tick()) !== false) {
					yield $tick;
				}
			}
		}
	}

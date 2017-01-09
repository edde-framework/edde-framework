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
		/**
		 * @var resource[]
		 */
		protected $connectionList;

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
			$this->offline();
			usleep(50);
			foreach ($this->connectionList as $client) {
				fflush($client);
				stream_socket_shutdown($client, STREAM_SHUT_RDWR);
				fclose($client);
			}
			$this->connectionList = null;
			fflush($this->stream);
			stream_socket_shutdown($this->stream, STREAM_SHUT_RDWR);
			fclose($this->stream);
			$this->stream = null;
		}

		public function tick() {
			$read = $this->connectionList;
			$read[] = $this->stream;
			$write = $except = [$this->stream];
			if (($select = stream_select($read, $write, $except, 256000)) === false) {
				throw new StreamServerException('Stream select has failed.');
			} else if ($select === 0) {
				/**
				 * nothing happened
				 */
				return false;
			}
			if (($index = array_search($this->stream, $read, true)) !== false) {
				unset($read[$index]);
				if (($handle = stream_socket_accept($this->stream)) !== false) {
					// a new client
					$this->connectionList[] = $handle;
				}
			}
			foreach ($read as $stream) {
				/**
				 * stream closed
				 */
				if (feof($stream)) {
					stream_socket_shutdown($this->connectionList[$index = array_search($stream, $this->connectionList, true)], STREAM_SHUT_RDWR);
					fflush($this->connectionList[$index]);
					fclose($this->connectionList[$index]);
					unset($this->connectionList[$index]);
					continue;
				}
			}
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

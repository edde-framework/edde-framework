<?php
	declare(strict_types = 1);

	use Edde\Common\Stream\StreamServer;

	require_once __DIR__ . '/loader.php';

	$server = new StreamServer();
	$server->server('tcp://0.0.0.0:8080');
	$server->close();



	//	class StreamException extends \Exception {
	//	}
	//
	//	class StreamServerException extends StreamException {
	//	}
	//
	//	class StreamClientException extends StreamException {
	//	}
	//
	//	class StreamObject {
	//		/**
	//		 * @var resource
	//		 */
	//		protected $stream;
	//
	//		public function close() {
	//			usleep(50);
	//			fflush($this->stream);
	//			stream_socket_shutdown($this->stream, STREAM_SHUT_RDWR);
	//			fclose($this->stream);
	//			$this->stream = null;
	//		}
	//
	//		static public function testServer() {
	//			$server = new StreamServer();
	//			printf("starting server on [%s]...", $address = 'tcp://0.0.0.0:4097');
	//			$server->server($address);
	//			printf(" ok\n");
	//			$server->run();
	//			$server->close();
	//		}
	//
	//		static public function testClient() {
	//			$client = new StreamClient();
	//			$client->client('tcp://10.0.0.10:4097');
	//			$client->write(str_repeat(0, 1024 * 1024));
	//			$client->close();
	//		}
	//	}
	//
	//	class StreamServer extends StreamObject {
	//		public function server(string $socket) {
	//			if (($this->stream = stream_socket_server($socket)) === false) {
	//				throw new StreamServerException('Server: Kaboom');
	//			}
	//			stream_set_blocking($this->stream, false);
	//		}
	//
	//		public function run() {
	//			$connectionList = [];
	//			while (true) {
	//				$read = $connectionList;
	//				$read[] = $this->stream;
	//				$write = $except = null;
	//				if (($select = stream_select($read, $write, $except, 256000)) === false) {
	//					throw new StreamServerException('Select: Kaboom');
	//				} else if ($select === 0) {
	//					continue;
	//				}
	//				if (($server = array_search($this->stream, $read, true)) !== false) {
	//					unset($read[$server]);
	//					if (($handle = stream_socket_accept($this->stream)) !== false) {
	//						printf("[%.4f] Connection from [%s]; current list count [%d]\n", microtime(true), stream_socket_get_name($handle, true), count($connectionList) + 1);
	//						fwrite($connectionList[] = $handle, "hello there");
	//					}
	//				}
	//				foreach ($read as $stream) {
	//					if (($length = strlen($string = stream_get_contents($stream))) > 0) {
	//						printf("client [%s], [%s] bytes\n", stream_socket_get_name($stream, true), $length);
	//					}
	//					if (feof($stream)) {
	//						printf("connection of [%s] closed; current list count [%d].\n", stream_socket_get_name($stream, true), count($connectionList) - 1);
	//						stream_socket_shutdown($connectionList[$index = array_search($stream, $connectionList)], STREAM_SHUT_RDWR);
	//						fclose($connectionList[$index]);
	//						unset($connectionList[$index]);
	//						continue;
	//					}
	//				}
	//			}
	//		}
	//	}
	//
	//	class StreamClient extends StreamObject {
	//		public function client(string $socket) {
	//			if (($this->stream = stream_socket_client($socket)) === false) {
	//				throw new StreamClientException('Client: Kaboom');
	//			}
	//			stream_set_blocking($this->stream, false);
	//		}
	//
	//		public function write(string $write) {
	//			printf("sending [%d] bytes\n", $length = strlen($write));
	//			$count = 0;
	//			$limit = 2048;
	//			// use socket_select; there can be data read; share this with server data read; server/client should be almost same
	//			while (($count += fwrite($this->stream, substr($write, $count, $limit), $limit)) < $length) {
	//				;
	//			}
	//		}
	//	}
	//
	//	class Stream {
	//		/**
	//		 * @var resource
	//		 */
	//		protected $stream;
	//
	//		public function __construct($stream) {
	//			$this->stream = $stream;
	//		}
	//
	//		public function run() {
	//			$connectionList = [];
	//
	//			while (true) {
	//				$read = $connectionList;
	//				$read[] = $this->stream;
	//			}
	//		}
	//	}
	//
	//	StreamServer::testServer();
	//	//	StreamServer::testClient();

<?php
	declare(strict_types = 1);

	namespace Edde\Common\Http;

	use phpunit\framework\TestCase;

	class HttpMessageTest extends TestCase {
		public function testEmptyMessage() {
			$message = new HttpMessage('', '');
			self::assertFalse($message->isUsed(), 'Message has been used!');
			self::assertEquals('application/octet-stream', $message->getContentType());
			self::assertTrue($message->isUsed(), 'Message has NOT been used!');
		}

		public function testSimpleMessage() {
			$message = new HttpMessage('here is plain text', 'Content-Type: text/plain');
			self::assertEquals('text/plain', $message->getContentType());
		}

		public function testComplexMessage() {
			$message = new HttpMessage(file_get_contents(__DIR__ . '/assets/complex-message.txt'), 'Content-Type: Multipart/Related; boundary="==r4SdGZrQQHDyuSuLOgmDmYbIsG7opnvoWQE2nVPK0e6wN3vxXhEzykf/aBRR=="; type="application/xop+xml"; start="<d569a93d-2406-4130-ba60-a61cb17f2818@uuid>"; start-info="application/soap+xml"');
			self::assertEquals('multipart/related', $message->getContentType());
			self::assertEquals([
				'boundary' => '==r4SdGZrQQHDyuSuLOgmDmYbIsG7opnvoWQE2nVPK0e6wN3vxXhEzykf/aBRR==',
				'type' => 'application/xop+xml',
				'start' => '<d569a93d-2406-4130-ba60-a61cb17f2818@uuid>',
				'start-info' => 'application/soap+xml',
			], $message->getHeaderList()
				->getContentType()
				->getParameterList());
		}
	}

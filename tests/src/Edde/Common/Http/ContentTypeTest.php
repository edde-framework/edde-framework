<?php
	declare(strict_types = 1);

	namespace Edde\Common\Http;

	use Edde\Api\Http\IContentType;
	use phpunit\framework\TestCase;

	class ContentTypeTest extends TestCase {
		public function testComlexContentType() {
			/** @var $contentType IContentType */
			$contentType = new ContentType('Multipart/Related; boundary="==r4SdGZrQQHDyuSuLOgmDmYbIsG7opnvoWQE2nVPK0e6wN3vxXhEzykf/aBRR=="; type="application/xop+xml"; start="<d569a93d-2406-4130-ba60-a61cb17f2818@uuid>"; start-info="application/soap+xml"');
			self::assertFalse($contentType->isUsed(), 'Content type has been used!');
			self::assertEquals('multipart/related', (string)$contentType);
			self::assertTrue($contentType->isUsed(), 'Content type has NOT been used!');
			self::assertEquals('windows-1250', $contentType->getCharset('windows-1250'));
			self::assertEquals([
				'boundary' => '==r4SdGZrQQHDyuSuLOgmDmYbIsG7opnvoWQE2nVPK0e6wN3vxXhEzykf/aBRR==',
				'type' => 'application/xop+xml',
				'start' => '<d569a93d-2406-4130-ba60-a61cb17f2818@uuid>',
				'start-info' => 'application/soap+xml',
			], $contentType->array());
		}
	}

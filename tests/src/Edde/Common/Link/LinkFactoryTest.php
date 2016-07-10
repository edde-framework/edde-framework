<?php
	namespace Edde\Common\Link;

	use Edde\Api\Url\IUrl;
	use phpunit\framework\TestCase;

	class LinkFactoryTest extends TestCase {
		public function testCommon() {
			$linkFactory = new LinkFactory();
			self::assertInstanceOf(IUrl::class, $uri = $linkFactory->linkTo($this, 'testCommon'));
			self::assertEquals('/edde/common/link/link-factory-test/test-common', $uri->getPath());
		}
	}

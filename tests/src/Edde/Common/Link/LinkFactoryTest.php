<?php
	declare(strict_types = 1);

	namespace Edde\Common\Link;

	use Edde\Api\Link\ILinkFactory;
	use Edde\Api\Url\IUrl;
	use phpunit\framework\TestCase;

	class LinkFactoryTest extends TestCase {
		/**
		 * @var ILinkFactory
		 */
		protected $linkFactory;

		public function testCommon() {
			self::assertInstanceOf(IUrl::class, $uri = $this->linkFactory->linkTo($this, 'testCommon'));
			self::assertEquals('/edde/common/link/link-factory-test/test-common', $uri->getPath());
		}

		protected function setUp() {
			$this->linkFactory = new LinkFactory();
		}
	}

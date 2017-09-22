<?php
	declare(strict_types=1);

	namespace Edde\Common\Utils;

	use Edde\Api\Utils\Inject\HttpUtils;
	use Edde\Ext\Test\TestCase;

	class HttpUtilsTest extends TestCase {
		use HttpUtils;

		public function testAccept() {
			self::assertSame(['*/*'], $this->httpUtils->accept());
			self::assertSame(['*/*'], $this->httpUtils->accept('text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,image/apng,*/*;q=0.8'));
		}
	}

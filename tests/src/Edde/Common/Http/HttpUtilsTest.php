<?php
	declare(strict_types = 1);

	namespace Edde\Common\Http;

	use phpunit\framework\TestCase;

	class HttpUtilsTest extends TestCase {
		public function testNullAcceptHeader() {
			self::assertEquals([
				'*/*',
			], HttpUtils::accept(null));
		}

		public function testAcceptHeader() {
			self::assertEquals([
				'text/html',
				'application/xhtml+xml',
				'image/webp',
				'application/xml',
				'*/*',
			], HttpUtils::accept('text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8'));
		}
	}

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

		public function testAcceptHeader2() {
			self::assertEquals([
				'audio/basic',
				'audio/*',
			], HttpUtils::accept('audio/*; q=0.2, audio/basic'));
		}

		public function testAcceptHeader3() {
			self::assertEquals([
				'text/html',
				'text/x-c',
				'text/x-dvi',
				'text/plain',
			], HttpUtils::accept('text/plain; q=0.5, text/html, text/x-dvi; q=0.8, text/x-c'));
		}
	}

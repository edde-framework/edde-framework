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

		public function testAcceptHeader4() {
			self::assertEquals([
				'text/plain',
				'text/html',
				'application/json',
				'*/*',
			], HttpUtils::accept('text/plain, application/json;q=0.5, text/html, */*;q=0.1'));
		}

		public function testAcceptHeader5() {
			self::assertEquals([
				'text/plain',
				'text/html',
				'application/json',
			], HttpUtils::accept('text/plain, application/json;q=0.5, text/html, text/drop;q=0'));
		}

		public function testAcceptHeader6() {
			self::assertEquals([
				'text/plain;format=flowed',
				'text/plain',
				'text/*',
				'*/*',
			], HttpUtils::accept('text/*, text/plain, text/plain;format=flowed, */*'));
		}

		public function testLanguage1() {
			self::assertEquals([
				'en',
			], HttpUtils::language(null, 'en'));
		}

		public function testLanguage2() {
			self::assertEquals([
				'da',
				'en-gb',
				'en',
			], HttpUtils::language('da, en-gb;q=0.8, en;q=0.7', 'en'));
		}

		public function testCharset1() {
			self::assertEquals([
				'utf-8',
			], HttpUtils::charset(null, 'utf-8'));
		}

		public function testCharset2() {
			self::assertEquals([
				'iso-8859-5',
				'unicode-1-1',
			], HttpUtils::charset('iso-8859-5, unicode-1-1;q=0.8', 'utf-8'));
		}
	}

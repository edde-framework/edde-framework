<?php
	declare(strict_types=1);

	namespace Edde\Common\Utils;

	use Edde\Api\Utils\Inject\HttpUtils;
	use Edde\Ext\Test\TestCase;

	class HttpUtilsTest extends TestCase {
		use HttpUtils;

		public function testAccept() {
			self::assertSame(['*/*'], $this->httpUtils->accept());
			self::assertSame([
				'text/html',
				'application/xhtml+xml',
				'image/webp',
				'image/apng',
				'application/xml',
				'*/*',
			], $this->httpUtils->accept('text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,image/apng,*/*;q=0.8'));
		}

		public function testLanguage() {
			self::assertSame(['en'], $this->httpUtils->language());
			self::assertSame([
				'cs',
				'en',
			], $this->httpUtils->language('cs,en;q=0.8'));
		}

		public function testContentType() {
			self::assertEquals((object)[
				'mime' => 'text/html',
				'params' => [
					'charset' => 'utf-8',
				],
			], $this->httpUtils->contentType('text/html; charset=utf-8'));
		}

		public function testCookie() {
			self::assertEquals((object)[
				'name' => '_gh_sess',
				'value' => 'eyJsYXN0MiLCJsabd2a61',
				'path' => '/',
				'secure' => true,
				'httpOnly' => true,
			], $this->httpUtils->cookie('_gh_sess=eyJsYXN0MiLCJsabd2a61; path=/; secure; HttpOnly'));
			self::assertEquals((object)[
				'name' => '__Host-user_session_same_site',
				'value' => 'nhTbc44ff4l3Lwxj',
				'path' => '/',
				'expires' => 'Fri, 06 Oct 2017 09:41:23 -0000',
				'secure' => true,
				'httpOnly' => true,
			], $this->httpUtils->cookie('__Host-user_session_same_site=nhTbc44ff4l3Lwxj; path=/; expires=Fri, 06 Oct 2017 09:41:23 -0000; secure; HttpOnly'));
		}

		public function testHttp() {
			self::assertEquals((object)[
				'method' => 'GET',
				'path' => '/edde-framework/edde-framework',
				'http' => '1.1',
			], $this->httpUtils->http('GET /edde-framework/edde-framework HTTP/1.1'));
			self::assertEquals((object)[
				'http' => '1.1',
				'status' => '200',
				'message' => 'OK',
			], $this->httpUtils->http('HTTP/1.1 200 OK'));
		}

		public function testHeaderList() {
			$headers = str_replace("\n", "\r\n", 'GET /edde-framework/edde-framework HTTP/1.1
Host: github.com
Connection: keep-alive
Cache-Control: max-age=0
Content-Type: text/html; charset=utf-8
Upgrade-Insecure-Requests: 1
User-Agent: Mozilla/5.0 (Windows NT 10.0; Win87; x87) AppleWebKit/527.36 (KHTML, like Gecko) Chrome/14.0.4173.241 Safari/317.35
Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,image/apng,*/*;q=0.8
DNT: 1
Referer: https://github.com/edde-framework/edde-framework
Accept-Encoding: gzip, deflate, br
Accept-Language: cs,en;q=0.8');
			self::assertEquals([
				'http' => 'GET /edde-framework/edde-framework HTTP/1.1',
				'Host' => 'github.com',
				'Connection' => 'keep-alive',
				'Cache-Control' => 'max-age=0',
				'Content-Type' => 'text/html; charset=utf-8',
				'Upgrade-Insecure-Requests' => '1',
				'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win87; x87) AppleWebKit/527.36 (KHTML, like Gecko) Chrome/14.0.4173.241 Safari/317.35',
				'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,image/apng,*/*;q=0.8',
				'DNT' => '1',
				'Referer' => 'https://github.com/edde-framework/edde-framework',
				'Accept-Encoding' => 'gzip, deflate, br',
				'Accept-Language' => 'cs,en;q=0.8',
			], $this->httpUtils->headerList($headers, false));
			self::assertEquals([
				'http' => (object)[
					'method' => 'GET',
					'path' => '/edde-framework/edde-framework',
					'http' => '1.1',
				],
				'Host' => 'github.com',
				'Connection' => 'keep-alive',
				'Cache-Control' => 'max-age=0',
				'Content-Type' => (object)[
					'mime' => 'text/html',
					'params' => [
						'charset' => 'utf-8',
					],
				],
				'Upgrade-Insecure-Requests' => '1',
				'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win87; x87) AppleWebKit/527.36 (KHTML, like Gecko) Chrome/14.0.4173.241 Safari/317.35',
				'Accept' => [
					'text/html',
					'application/xhtml+xml',
					'image/webp',
					'image/apng',
					'application/xml',
					'*/*',
				],
				'DNT' => '1',
				'Referer' => 'https://github.com/edde-framework/edde-framework',
				'Accept-Encoding' => 'gzip, deflate, br',
				'Accept-Language' => [
					'cs',
					'en',
				],
			], $this->httpUtils->headerList($headers));
		}
	}

<?php
	declare(strict_types=1);

	namespace Edde\Common\Http\Client;

	use Edde\Api\Http\Client\LazyHttpClientTrait;
	use Edde\Ext\Test\TestCase;

	/**
	 * @group http
	 * @group wip
	 */
	class HttpClientTest extends TestCase {
		use LazyHttpClientTrait;

		public function testGet() {
			// $result = $this->httpClient->post('http://httpbin.org/post')->post(['foo' => 'bar'])->execute();
		}
	}

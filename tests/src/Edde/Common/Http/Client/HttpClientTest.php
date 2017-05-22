<?php
	declare(strict_types = 1);

	namespace Edde\Common\Http\Client;

	use Edde\Api\Http\Client\LazyHttpClientTrait;
	use Edde\Ext\Container\ContainerFactory;
	use Edde\Ext\Test\TestCase;

	class HttpClientTest extends TestCase {
		use LazyHttpClientTrait;

		public function testFoo() {
			$response = $this->httpClient->get('https://httpbin.org/get')
				->execute()
				->convert(['array']);
			$this->assertTrue(true);
		}

		protected function setUp() {
			ContainerFactory::autowire($this);
		}
	}
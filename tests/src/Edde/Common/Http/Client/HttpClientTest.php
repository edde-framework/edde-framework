<?php
	declare(strict_types=1);

	namespace Edde\Common\Http\Client;

	use Edde\Api\File\IRootDirectory;
	use Edde\Api\Http\Client\IHttpClient;
	use Edde\Common\File\RootDirectory;
	use Edde\Ext\Container\ContainerFactory;
	use PHPUnit\Framework\TestCase;

	/**
	 * @group http
	 * @group wip
	 */
	class HttpClientTest extends TestCase {
		/**
		 * @var IHttpClient
		 */
		protected $httpClient;

		public function testGet() {
			$this->httpClient->gete('http://127.0.0.1/v1/user');
		}

		protected function setUp() {
			$this->httpClient = ContainerFactory::container([
				IRootDirectory::class => new RootDirectory(__DIR__),
			])
				->create(IHttpClient::class, [], __METHOD__);
		}
	}

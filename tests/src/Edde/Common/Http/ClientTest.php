<?php
	declare(strict_types = 1);

	namespace Edde\Common\Http;

	use Edde\Api\Converter\IConverterManager;
	use Edde\Api\Http\IClient;
	use Edde\Common\Converter\ConverterManager;
	use Edde\Ext\Container\ContainerFactory;
	use Edde\Ext\Converter\JsonConverter;
	use phpunit\framework\TestCase;

	class ClientTest extends TestCase {
		/**
		 * @var IClient
		 */
		protected $client;

		public function testGet() {
			$httpResponse = $this->client->get('https://gitlab.com/api/v3/projects?private_token=oX5sbYw4w-unLxgwxJWR');
			self::assertNotEmpty($array = $httpResponse->getBody()
				->convert('array'));
			self::assertTrue(is_array($array));
		}

		protected function setUp() {
			$container = ContainerFactory::create([
				IClient::class => Client::class,
				IConverterManager::class => ConverterManager::class,
			]);
			$converterManager = $container->create(IConverterManager::class);
			$converterManager->registerConverter($container->create(JsonConverter::class));
			$this->client = $container->create(IClient::class);
		}
	}

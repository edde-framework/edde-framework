<?php
	declare(strict_types = 1);

	namespace Edde\Common\Converter;

	use Edde\Api\Converter\ConverterException;
	use Edde\Api\Converter\IConverterManager;
	use Edde\Ext\Container\ContainerFactory;
	use phpunit\framework\TestCase;

	require_once(__DIR__ . '/assets/assets.php');

	class ConverterManagerTest extends TestCase {
		/**
		 * @var IConverterManager
		 */
		protected $converterManager;

		public function testUnknownConverter() {
			$this->expectException(ConverterException::class);
			$this->expectExceptionMessage('Cannot convert unknown source mime [unknown source] to [json].');
			$this->converterManager->convert('something here', 'unknown source', 'json');
		}

		public function testConflictException() {
			$this->expectException(ConverterException::class);
			$this->expectExceptionMessage('Converter [CleverConverter] has conflict with converter [DummyConverter] on mime [bar].');
			$this->converterManager->registerConverter(new \DummyConverter([
				'foo',
				'bar',
			]));
			$this->converterManager->registerConverter(new \CleverConverter([
				'bar',
				'foobar',
			]));
		}

		public function testDummyConverter() {
			self::assertEquals($expect = 'this will be null on output', $this->converterManager->convert($expect, 'boo', 'something'));
		}

		protected function setUp() {
			$container = ContainerFactory::create([
				IConverterManager::class => ConverterManager::class,
			]);
			$this->converterManager = $container->create(IConverterManager::class);
			$this->converterManager->registerConverter(new \DummyConverter(['boo']));
		}
	}

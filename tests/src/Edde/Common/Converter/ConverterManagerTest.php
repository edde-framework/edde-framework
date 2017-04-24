<?php
	declare(strict_types=1);

	namespace Edde\Common\Converter;

	use Edde\Api\Converter\ConverterException;
	use Edde\Api\Converter\IConverterManager;
	use Edde\Api\File\IRootDirectory;
	use Edde\Common\File\RootDirectory;
	use Edde\Ext\Container\ClassFactory;
	use Edde\Ext\Container\ContainerFactory;
	use Edde\Ext\Converter\ConverterManagerConfigurator;
	use PHPUnit\Framework\TestCase;

	class ConverterManagerTest extends TestCase {
		/**
		 * @var IConverterManager
		 */
		protected $converterManager;

		public function testTargetArray() {
			self::assertEquals(json_encode($source = ['foo']), $this->converterManager->convert($source, 'array', [
				'string',
				'text/plain',
				'json',
			])
				->convert());
		}

		public function testKaboom() {
			$this->expectException(ConverterException::class);
			$this->expectExceptionMessage('Cannot convert unknown/unsupported source mime [array] to any of [string, text/plain].');
			self::assertEquals(json_encode($source = ['foo']), $this->converterManager->convert($source, 'array', [
				'string',
				'text/plain',
			])
				->convert());
		}

		protected function setUp() {
			$this->converterManager = ContainerFactory::container([
				IRootDirectory::class => ContainerFactory::instance(RootDirectory::class, [__DIR__]),
				new ClassFactory(),
			], [
				IConverterManager::class => ConverterManagerConfigurator::class,
			])
				->create(IConverterManager::class);
			$this->converterManager->setup();
		}
	}

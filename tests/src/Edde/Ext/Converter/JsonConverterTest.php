<?php
	declare(strict_types = 1);

	namespace Edde\Ext\Converter;

	use Edde\Api\Converter\ConverterException;
	use Edde\Api\Converter\IConverter;
	use phpunit\framework\TestCase;

	class JsonConverterTest extends TestCase {
		/**
		 * @var IConverter
		 */
		protected $converter;

		public function testMimeList() {
			self::assertEquals([
				'application/json',
				'json',
			], $this->converter->getMimeList());
		}

		public function testConvert() {
			self::assertEquals($expect = ['foo' => true], $this->converter->convert(json_encode($expect), 'array'));
			$expect = new \stdClass();
			$expect->foo = true;
			self::assertEquals($expect, $this->converter->convert(json_encode($expect), 'object'));
		}

		public function testException() {
			$this->expectException(ConverterException::class);
			$this->expectExceptionMessage(sprintf('Unsuported convertion in [%s] from [application/json, json] to [my-mime/here].', JsonConverter::class));
			$this->converter->convert('foo', 'my-mime/here');
		}

		protected function setUp() {
			$this->converter = new JsonConverter();
		}
	}

<?php
	declare(strict_types = 1);

	namespace Edde\Ext\Converter;

	use Edde\Api\Converter\ConverterException;
	use Edde\Api\Converter\IConverter;
	use Edde\Api\Node\INode;
	use Edde\Common\File\File;
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

		public function testNodeConvert() {
			self::assertInstanceOf(INode::class, $source = $this->converter->convert(new File(__DIR__ . '/assets/sample.json'), 'node'));
			self::assertEquals('foo', $source->getName());
			self::assertEquals('moo', $source->getValue());
			self::assertEquals([
				'foo' => 'foo',
				'poo' => 'poo',
				'bar' => 'bar',
			], $source->getAttributeList());
			self::assertEquals([
				'meta' => 'list',
			], $source->getMetaList());
		}

		public function testException() {
			$this->expectException(ConverterException::class);
			$this->expectExceptionMessage(sprintf('Unsuported conversion in [%s] from [application/json, json] to [my-mime/here].', JsonConverter::class));
			$this->converter->convert('foo', 'my-mime/here');
		}

		protected function setUp() {
			$this->converter = new JsonConverter();
		}
	}

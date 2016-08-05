<?php
	declare(strict_types = 1);

	namespace Edde\Common\Xml;

	use Edde\Api\Xml\IXmlParser;
	use Edde\Common\Resource\FileResource;
	use phpunit\framework\TestCase;

	require_once(__DIR__ . '/assets/assets.php');

	class XmlParserTest extends TestCase {
		/**
		 * @var IXmlParser
		 */
		protected $xmlParser;

		public function testSimple() {
			$this->xmlParser->parse(new FileResource(__DIR__ . '/assets/simple.xml'), $handler = new \TestXmlHandler());
			self::assertEquals([
				[
					'root',
					[],
				],
			], $handler->getTagList());
		}

		public function testSimpleShort() {
			$this->xmlParser->parse(new FileResource(__DIR__ . '/assets/simple-short.xml'), $handler = new \TestXmlHandler());
			self::assertEquals([
				[
					'root',
					[],
				],
			], $handler->getTagList());
		}

		public function testSimpleAttribute() {
			$this->xmlParser->parse(new FileResource(__DIR__ . '/assets/simple-attribute.xml'), $handler = new \TestXmlHandler());
			self::assertEquals([
				[
					'root',
					[
						'foo' => 'bar',
						'bar' => 'foo',
						'class' => 'Some\Strange\Characters',
					],
				],
			], $handler->getTagList());
		}

		public function testSimpleShortAttribute() {
			$this->xmlParser->parse(new FileResource(__DIR__ . '/assets/simple-short-attribute.xml'), $handler = new \TestXmlHandler());
			self::assertEquals([
				[
					'root',
					[
						'foo' => 'bar',
						'bar' => 'foo',
						'class' => 'Some\Strange\Characters',
					],
				],
			], $handler->getTagList());
		}

		public function testBitLessSimple() {
			$this->xmlParser->parse(new FileResource(__DIR__ . '/assets/a-bit-less-simple.xml'), $handler = new \TestXmlHandler());
			self::assertEquals([
				[
					'root',
					[
						'r' => 'oot',
					],
				],
				[
					'item',
					[],
				],
				[
					'item',
					['koo' => 'poo'],
				],
				[
					'internal',
					[],
				],
				[
					'hidden-tag',
					[],
				],
			], $handler->getTagList());
		}

		protected function setUp() {
			$this->xmlParser = new XmlParser();
		}
	}

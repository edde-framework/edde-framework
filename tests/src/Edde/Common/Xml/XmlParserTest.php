<?php
	declare(strict_types = 1);

	namespace Edde\Common\Xml;

	use Edde\Api\Xml\IXmlParser;
	use Edde\Common\Resource\FileResource;
	use Edde\Common\Resource\ResourceManager;
	use phpunit\framework\TestCase;

	require_once(__DIR__ . '/assets/assets.php');

	class XmlParserTest extends TestCase {
		/**
		 * @var IXmlParser
		 */
		protected $xmlParser;

		public function testSimple() {
			$this->xmlParser->file(__DIR__ . '/assets/simple.xml', $handler = new \TestXmlHandler());
			self::assertEquals([
				[
					'root',
					[],
				],
			], $handler->getTagList());
		}

		public function testSimpleShort() {
			$this->xmlParser->file(__DIR__ . '/assets/simple-short.xml', $handler = new \TestXmlHandler());
			self::assertEquals([
				[
					'root',
					[],
				],
			], $handler->getTagList());
		}

		public function testSimpleAttribute() {
			$this->xmlParser->file(__DIR__ . '/assets/simple-attribute.xml', $handler = new \TestXmlHandler());
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
			$this->xmlParser->file(__DIR__ . '/assets/simple-short-attribute.xml', $handler = new \TestXmlHandler());
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
			$this->xmlParser->file(__DIR__ . '/assets/a-bit-less-simple.xml', $handler = new \TestXmlHandler());
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
					'item2',
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
				[
					'tag-with-value',
					[],
				],
			], $handler->getTagList());
		}

		public function testComment() {
			$this->xmlParser->file(__DIR__ . '/assets/comment-test.xml', $handler = new \TestXmlHandler());
			self::assertEquals([
				[
					'node',
					[],
				],
				[
					'poo',
					[],
				],
			], $handler->getTagList());
		}

		public function testMimeType() {
			$file = new FileResource(__DIR__ . '/assets/simple.xml');
			self::assertEquals('text/xml', $file->getMime());
		}

		public function testParserNode() {
			$resourceManager = new ResourceManager();
			$resourceManager->registerResourceHandler($xmlResourceHandlder = new XmlResourceHandler($this->xmlParser));
			$node = $resourceManager->file(__DIR__ . '/assets/a-bit-less-simple.xml');
			self::assertEquals('root', $node->getName());
			self::assertEquals(['r' => 'oot'], $node->getAttributeList());
			self::assertCount(3, $node->getNodeList());
			$nodeIterator = new \ArrayIterator($node->getNodeList());
			$nodeIterator->rewind();
			self::assertEquals('item', $nodeIterator->current()
				->getName());
			$nodeIterator->next();
			self::assertEquals('item2', $nodeIterator->current()
				->getName());
			$nodeIterator->next();
			self::assertEquals('internal', $nodeIterator->current()
				->getName());
		}

		protected function setUp() {
			$this->xmlParser = new XmlParser();
		}
	}

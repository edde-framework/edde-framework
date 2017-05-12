<?php
	declare(strict_types=1);

	namespace Edde\Common\Node;

	use Edde\Api\Converter\LazyConverterManagerTrait;
	use Edde\Api\Node\INode;
	use Edde\Ext\Test\TestCase;

	class NodeUtilsTest extends TestCase {
		use LazyConverterManagerTrait;
		static protected $source = '
		{
		        "packet": {
		                "version": "1.0",
		                "id": "some-guid-of-this-packet-even-this-text-could-be-used!",
		                "elements": [
		                        {
		                                "name": "request",
		                                "id": "aa-bb-cc",
		                                "request": "//some.namespace/execute-this",
		                                "foo": "bar",
		                                "name-of-node": {}
		                        },
		                        {
		                                "name": "request",
		                                "id": "aa-dd-cc",
		                                "request": "//some.namespace/another-request"
		                        },
		                        {
		                                "name": "event",
		                                "id": "cc-bb-aa",
		                                "event": "//namespace.of.event/yapee"
		                        }
		                ]
		        }
		}';

		protected function doTest(INode $root) {
			$node = NodeQuery::first($root, '//packet');
			self::assertNotEmpty($node);
			self::assertEquals('packet', $node->getName());
			self::assertEquals('1.0', $node->getAttribute('version'));
			self::assertEquals('some-guid-of-this-packet-even-this-text-could-be-used!', $node->getAttribute('id'));
			self::assertCount(1, $node->getNodeList());

			$node = NodeQuery::first($root, '//packet/elements/*');
			self::assertEquals('request', $node->getName());
			self::assertEquals('aa-bb-cc', $node->getAttribute('id'));
			self::assertEquals('//some.namespace/execute-this', $node->getAttribute('request'));
			self::assertEquals('bar', $node->getAttribute('foo'));
			self::assertCount(1, $node->getNodeList());

			$node = NodeQuery::first($root, '//packet/elements/event');
			self::assertEquals('event', $node->getName());
			self::assertEquals('cc-bb-aa', $node->getAttribute('id'));
			self::assertEquals('//namespace.of.event/yapee', $node->getAttribute('event'));
			self::assertCount(0, $node->getNodeList());
		}

		public function testSimpleConvert() {
			$this->doTest(NodeUtils::convert($this->converterManager->convert(self::$source, 'application/json', ['object'])->convert()));
		}

		public function testConverterManager() {
			$this->doTest($this->converterManager->convert(self::$source, 'application/json', [INode::class])->convert());
		}
	}

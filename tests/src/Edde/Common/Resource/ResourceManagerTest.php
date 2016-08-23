<?php
	declare(strict_types = 1);

	namespace Edde\Common\Resource;

	use Edde\Api\Node\INode;
	use Edde\Api\Resource\IResourceManager;
	use Edde\Common\Xml\XmlParser;
	use Edde\Common\Xml\XmlResourceHandler;
	use Edde\Ext\Resource\JsonResourceHandler;
	use Edde\Ext\Resource\PhpResourceHandler;
	use phpunit\framework\TestCase;

	class ResourceManagerTest extends TestCase {
		/**
		 * @var IResourceManager
		 */
		protected $resourceManager;

		public function testJsonResourceHandler() {
			self::assertInstanceOf(INode::class, $source = $this->resourceManager->file(__DIR__ . '/assets/sample.json'));
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

		protected function setUp() {
			$this->resourceManager = new ResourceManager();
			$this->resourceManager->registerResourceHandler(new JsonResourceHandler());
			$this->resourceManager->registerResourceHandler(new XmlResourceHandler(new XmlParser()));
			$this->resourceManager->registerResourceHandler(new PhpResourceHandler());
		}
	}

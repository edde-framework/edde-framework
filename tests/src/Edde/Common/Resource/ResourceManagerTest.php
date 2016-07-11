<?php
	namespace Edde\Common\Resource;

	use Edde\Api\Resource\IResourceQuery;
	use Edde\Api\Storage\IStorable;
	use Edde\Common\Database\DatabaseStorage;
	use Edde\Common\Schema\SchemaManager;
	use Edde\Ext\Resource\Scanner\FilesystemScanner;
	use phpunit\framework\TestCase;

	class ResourceManagerTest extends TestCase {
		public function testUpdate() {
			$resourceManager = $this->createResourceManager();
			$resourceManager->update();
		}

		protected function createResourceManager() {
			$schemaManager = new SchemaManager();
			$schemaManager->addSchema(new ResourceSchema());
			return new ResourceManager($schemaManager, new DatabaseStorage(), new FilesystemScanner(__DIR__ . '/assets'));
		}

		public function testCommon() {
			$resourceManager = $this->createResourceManager();
			self::assertInstanceOf(IStorable::class, $resourceManager->createResourceStorable());
			self::assertInstanceOf(IResourceQuery::class, $resourceQuery = $resourceManager->createResourceQuery());
		}
	}

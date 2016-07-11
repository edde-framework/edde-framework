<?php
	namespace Edde\Common\Resource;

	use Edde\Api\Resource\IResourceManager;
	use Edde\Api\Resource\IResourceQuery;
	use Edde\Api\Resource\Scanner\IScanner;
	use Edde\Api\Schema\ISchema;
	use Edde\Api\Schema\ISchemaManager;
	use Edde\Api\Storage\IStorage;
	use Edde\Common\Usable\AbstractUsable;

	class ResourceManager extends AbstractUsable implements IResourceManager {
		/**
		 * @var ISchemaManager
		 */
		protected $schemaManager;
		/**
		 * @var IStorage
		 */
		protected $storage;
		/**
		 * @var IScanner
		 */
		protected $scanner;
		/**
		 * @var ISchema
		 */
		protected $resourceSchema;

		/**
		 * @param ISchemaManager $schemaManager
		 * @param IStorage $storage where to put scanned resources
		 * @param IScanner $scanner current resource scanner
		 */
		public function __construct(ISchemaManager $schemaManager, IStorage $storage, IScanner $scanner) {
			$this->schemaManager = $schemaManager;
			$this->storage = $storage;
			$this->scanner = $scanner;
		}

		public function update() {
			foreach ($this->scanner->scan() as $resource) {
				$resourceStorable = $this->createResourceStorable();
				$this->storage->store($resourceStorable);
			}
		}

		public function createResourceStorable() {
			$this->usse();
			return new ResourceStorable($this->resourceSchema);
		}

		public function getResourceCollection(IResourceQuery $resourceQuery) {
			$this->usse();
			return $this->storage->collection($resourceQuery->getQuery());
		}

		public function getResource(IResourceQuery $resourceQuery) {
			$this->usse();
			return $this->storage->storable($resourceQuery->getQuery());
		}

		public function createResourceQuery() {
			$this->usse();
			return new ResourceQuery($this->resourceSchema);
		}

		protected function prepare() {
			$this->resourceSchema = $this->schemaManager->getSchema(Resource::class);
		}
	}

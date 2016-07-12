<?php
	namespace Edde\Common\Resource;

	use Edde\Api\Crypt\ICrypt;
	use Edde\Api\Resource\IResourceManager;
	use Edde\Api\Resource\IResourceQuery;
	use Edde\Api\Resource\Scanner\IScanner;
	use Edde\Api\Schema\ISchema;
	use Edde\Api\Schema\ISchemaManager;
	use Edde\Api\Storage\IStorableFactory;
	use Edde\Api\Storage\IStorage;
	use Edde\Common\Query\Delete\DeleteQuery;
	use Edde\Common\Usable\AbstractUsable;

	class ResourceManager extends AbstractUsable implements IResourceManager {
		/**
		 * @var IStorableFactory
		 */
		protected $storableFactory;
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
		 * @var ICrypt
		 */
		protected $crypt;
		/**
		 * @var ISchema
		 */
		protected $resourceSchema;

		/**
		 * @param IStorableFactory $storableFactory
		 * @param ISchemaManager $schemaManager
		 * @param IStorage $storage where to put scanned resources
		 * @param IScanner $scanner current resource scanner
		 * @param ICrypt $crypt as guid generator
		 */
		public function __construct(IStorableFactory $storableFactory, ISchemaManager $schemaManager, IStorage $storage, IScanner $scanner, ICrypt $crypt) {
			$this->storableFactory = $storableFactory;
			$this->schemaManager = $schemaManager;
			$this->storage = $storage;
			$this->scanner = $scanner;
			$this->crypt = $crypt;
		}

		public function update() {
			$this->usse();
			$this->storage->start();
			try {
				$this->storage->execute(new DeleteQuery($this->resourceSchema->getSchemaName()));
				foreach ($this->scanner->scan() as $resource) {
					$resourceStorable = $this->storableFactory->create(ResourceStorable::class, $this->resourceSchema);
					$resourceStorable->set('guid', $this->crypt->guid());
					$resourceStorable->set('url', (string)$resource->getUrl());
					$resourceStorable->set('name', $resource->getName());
					$resourceStorable->set('mime', $resource->getMime());
					$this->storage->store($resourceStorable);
				}
				$this->storage->commit();
			} catch (\Exception $e) {
				$this->storage->rollback();
				throw $e;
			}
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

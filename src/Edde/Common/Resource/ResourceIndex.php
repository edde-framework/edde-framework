<?php
	namespace Edde\Common\Resource;

	use Edde\Api\Container\IContainer;
	use Edde\Api\Crypt\ICryptEngine;
	use Edde\Api\Resource\IResource;
	use Edde\Api\Resource\IResourceIndex;
	use Edde\Api\Resource\IResourceQuery;
	use Edde\Api\Resource\IResourceStorable;
	use Edde\Api\Resource\Scanner\IScanner;
	use Edde\Api\Schema\ISchema;
	use Edde\Api\Schema\ISchemaManager;
	use Edde\Api\Storage\IStorage;
	use Edde\Api\Storage\StorageException;
	use Edde\Common\Query\Delete\DeleteQuery;
	use Edde\Common\Url\Url;
	use Edde\Common\Usable\AbstractUsable;

	class ResourceIndex extends AbstractUsable implements IResourceIndex {
		/**
		 * @var IContainer
		 */
		protected $container;
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
		 * @var ICryptEngine
		 */
		protected $cryptEngine;
		/**
		 * @var ISchema
		 */
		protected $resourceSchema;

		/**
		 * @param IContainer $container
		 * @param ISchemaManager $schemaManager
		 * @param IStorage $storage
		 * @param IScanner $scanner
		 * @param ICryptEngine $cryptcryptEngine
		 */
		public function __construct(IContainer $container, ISchemaManager $schemaManager, IStorage $storage, IScanner $scanner, ICryptEngine $cryptcryptEngine) {
			$this->container = $container;
			$this->schemaManager = $schemaManager;
			$this->storage = $storage;
			$this->scanner = $scanner;
			$this->cryptEngine = $cryptcryptEngine;
		}

		public function update() {
			$this->usse();
			$this->storage->start();
			try {
				$this->storage->execute(new DeleteQuery($this->resourceSchema->getSchemaName()));
				foreach ($this->scanner->scan() as $resource) {
					$this->save($resource);
				}
				$this->storage->commit();
			} catch (\Exception $e) {
				$this->storage->rollback();
				throw $e;
			}
			return $this;
		}

		public function save(IResource $resource) {
			$url = $resource->getUrl();
			$resourceStorable = $this->createResourceStorable();
			$resourceStorable->set('url', $url->getAbsoluteUrl());
			$resourceStorable->set('base', $resource->getBase());
			$resourceStorable->set('name', $resource->getName());
			$resourceStorable->set('extension', $url->getExtension());
			$resourceStorable->set('mime', $resource->getMime());
			$this->store($resourceStorable);
			return $this;
		}

		public function createResourceStorable() {
			$this->usse();
			$resourceStorable = new ResourceStorable($this->container);
			$resourceStorable->setSchema($this->resourceSchema);
			$resourceStorable->set('guid', $this->cryptEngine->guid());
			return $resourceStorable;
		}

		public function store(IResourceStorable $resourceStorable) {
			$this->storage->store($resourceStorable);
			return $this;
		}

		public function getResourceCollection(IResourceQuery $resourceQuery) {
			$this->usse();
			foreach ($this->storage->collection($this->resourceSchema, $resourceQuery->getQuery()) as $storable) {
				yield new Resource(Url::create($storable->get('url')), $storable->get('base'), $storable->get('name'), $storable->get('mime'));
			}
		}

		public function hasResource(IResourceQuery $resourceQuery) {
			$this->usse();
			try {
				$this->getResource($resourceQuery);
				return true;
			} catch (StorageException $e) {
			}
			return false;
		}

		public function getResource(IResourceQuery $resourceQuery) {
			$this->usse();
			$storable = $this->storage->storable($this->resourceSchema, $resourceQuery->getQuery());
			return new Resource(Url::create($storable->get('url')), $storable->get('base'), $storable->get('name'), $storable->get('mime'));
		}

		public function query() {
			$this->usse();
			return new ResourceQuery($this, $this->resourceSchema);
		}

		protected function prepare() {
			$this->resourceSchema = $this->schemaManager->getSchema(ResourceStorable::class);
		}
	}

<?php
	namespace Edde\Common\Storage;

	use Edde\Api\Query\IQuery;
	use Edde\Api\Schema\ISchema;
	use Edde\Api\Storage\ICollection;
	use Edde\Api\Storage\IStorableFactory;
	use Edde\Api\Storage\IStorage;
	use Edde\Common\AbstractObject;

	abstract class AbstractCollection extends AbstractObject implements ICollection {
		/**
		 * @var ISchema
		 */
		protected $schema;
		/**
		 * @var IStorage
		 */
		protected $storage;
		/**
		 * @var IStorableFactory
		 */
		protected $storableFactory;
		/**
		 * @var IQuery
		 */
		protected $query;

		/**
		 * @param ISchema $schema
		 * @param IStorage $storage
		 * @param IStorableFactory $storableFactory
		 * @param IQuery $query
		 */
		public function __construct(ISchema $schema, IStorage $storage, IStorableFactory $storableFactory, IQuery $query) {
			$this->schema = $schema;
			$this->storage = $storage;
			$this->storableFactory = $storableFactory;
			$this->query = $query;
		}

		public function getIterator() {
			$storableName = $this->schema->getSchemaName();
			foreach ($this->storage->execute($this->query) as $item) {
				$storable = $this->storableFactory->create($storableName);
				$storable->push((array)$item);
				yield $storable;
			}
		}
	}

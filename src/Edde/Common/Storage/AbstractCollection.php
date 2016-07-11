<?php
	namespace Edde\Common\Storage;

	use Edde\Api\Schema\ISchema;
	use Edde\Api\Storage\CollectionException;
	use Edde\Api\Storage\ICollection;
	use Edde\Api\Storage\IStorable;
	use Edde\Api\Storage\IStorage;

	abstract class AbstractCollection extends AbstractCollectionIterator implements ICollection {
		/**
		 * @var IStorage
		 */
		protected $storage;
		/**
		 * collection has some schema
		 *
		 * @var ISchema
		 */
		protected $schema;

		/**
		 * @param IStorage $storage
		 * @param ISchema $schema
		 */
		public function __construct(IStorage $storage, ISchema $schema) {
			$this->storage = $storage;
			$this->schema = $schema;
		}

		public function store(IStorable $storable) {
			if ($storable->getSchema() !== $this->schema) {
				$storableSchema = $storable->getSchema();
				throw new CollectionException(sprintf('Cannot store [%s] storable because of missmatched schemas - expected [%s], got [%s].', get_class($storable), $this->schema->getSchemaName(), $storableSchema->getSchemaName()));
			}
			$this->storage->store($storable);
			return $this;
		}
	}

<?php
	namespace Edde\Common\Storage;

	use Edde\Api\Query\IQuery;
	use Edde\Api\Schema\ISchema;
	use Edde\Api\Storage\IStorableFactory;
	use Edde\Api\Storage\IStorage;
	use Edde\Api\Storage\StorageException;
	use Edde\Common\Usable\AbstractUsable;

	abstract class AbstractStorage extends AbstractUsable implements IStorage {
		/**
		 * @var IStorableFactory
		 */
		protected $storableFactory;

		/**
		 * @param IStorableFactory $storableFactory
		 */
		public function __construct(IStorableFactory $storableFactory) {
			$this->storableFactory = $storableFactory;
		}

		public function storable(ISchema $schema, IQuery $query) {
			foreach ($this->collection($schema, $query) as $storable) {
				return $storable;
			}
			throw new StorageException(sprintf('Cannot retrieve any storable [%s] by the given query.', $schema->getSchemaName()));
		}

		public function collection(ISchema $schema, IQuery $query) {
			return new Collection($schema, $this, $this->storableFactory, $query);
		}

	}

<?php
	namespace Edde\Common\Storage;

	use Edde\Api\Container\IContainer;
	use Edde\Api\Query\IQuery;
	use Edde\Api\Schema\ISchema;
	use Edde\Api\Storage\ICollection;
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
		 * @var IContainer
		 */
		protected $container;
		/**
		 * @var IQuery
		 */
		protected $query;

		/**
		 * @param ISchema $schema
		 * @param IStorage $storage
		 * @param IContainer $container
		 * @param IQuery $query
		 */
		public function __construct(ISchema $schema, IStorage $storage, IContainer $container, IQuery $query) {
			$this->schema = $schema;
			$this->storage = $storage;
			$this->container = $container;
			$this->query = $query;
		}

		public function getIterator() {
			$storableName = $this->schema->getSchemaName();
			foreach ($this->storage->execute($this->query) as $item) {
				$storable = $this->container->create($storableName);
				$storable->push((array)$item);
				yield $storable;
			}
		}
	}

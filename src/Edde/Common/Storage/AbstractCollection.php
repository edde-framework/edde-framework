<?php
	declare(strict_types = 1);

	namespace Edde\Common\Storage;

	use Edde\Api\Container\IContainer;
	use Edde\Api\Query\IQuery;
	use Edde\Api\Schema\ISchema;
	use Edde\Api\Storage\ICollection;
	use Edde\Api\Storage\IStorage;
	use Edde\Common\AbstractObject;
	use Edde\Common\Crate\Crate;

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

		public function getQuery() {
			return $this->query;
		}

		public function getIterator() {
			$crateName = $this->container->has($crateName = $this->schema->getSchemaName()) ? $crateName : Crate::class;
			foreach ($this->storage->execute($this->query) as $item) {
				$crate = $this->container->create($crateName);
				$crate->setSchema($this->schema);
				$crate->push((array)$item);
				yield $crate;
			}
		}
	}

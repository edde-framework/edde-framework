<?php
	declare(strict_types = 1);

	namespace Edde\Common\Storage;

	use Edde\Api\Crate\ICrateFactory;
	use Edde\Api\Query\IQuery;
	use Edde\Api\Storage\ICollection;
	use Edde\Api\Storage\IStorage;
	use Edde\Common\AbstractObject;

	/**
	 * Default implementation of collection.
	 */
	class Collection extends AbstractObject implements ICollection {
		/**
		 * @var string
		 */
		protected $crate;
		/**
		 * @var IStorage
		 */
		protected $storage;
		/**
		 * @var ICrateFactory
		 */
		protected $crateFactory;
		/**
		 * @var IQuery
		 */
		protected $query;
		/**
		 * @var string
		 */
		protected $schema;

		/**
		 * @param string $crate
		 * @param IStorage $storage
		 * @param ICrateFactory $crateFactory
		 * @param IQuery $query
		 * @param string $schema
		 */
		public function __construct(string $crate, IStorage $storage, ICrateFactory $crateFactory, IQuery $query, string $schema = null) {
			$this->crate = $crate;
			$this->storage = $storage;
			$this->crateFactory = $crateFactory;
			$this->query = $query;
			$this->schema = $schema;
		}

		public function getQuery() {
			return $this->query;
		}

		public function getIterator() {
			foreach ($this->storage->execute($this->query) as $item) {
				yield $this->crateFactory->crate($this->crate, (array)$item, $this->schema);
			}
		}
	}

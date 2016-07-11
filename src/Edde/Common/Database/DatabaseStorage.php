<?php
	namespace Edde\Common\Database;

	use Edde\Api\Cache\ICache;
	use Edde\Api\Cache\ICacheFactory;
	use Edde\Api\Database\IDatabaseStorage;
	use Edde\Api\Database\IDriver;
	use Edde\Api\Node\INodeQuery;
	use Edde\Api\Query\IQuery;
	use Edde\Api\Storage\IStorable;
	use Edde\Common\Node\NodeQuery;
	use Edde\Common\Query\Insert\InsertQuery;
	use Edde\Common\Query\Select\SelectQuery;
	use Edde\Common\Query\Update\UpdateQuery;
	use Edde\Common\Storage\AbstractStorage;

	class DatabaseStorage extends AbstractStorage implements IDatabaseStorage {
		/**
		 * @var IDriver
		 */
		protected $driver;
		/**
		 * @var ICacheFactory
		 */
		protected $cacheFactory;
		/**
		 * @var ICache
		 */
		protected $cache;
		/**
		 * @var INodeQuery
		 */
		protected $sourceNodeQuery;

		/**
		 * @param IDriver $driver
		 * @param ICacheFactory $cacheFactory
		 */
		public function __construct(IDriver $driver, ICacheFactory $cacheFactory) {
			$this->driver = $driver;
			$this->cacheFactory = $cacheFactory;
		}

		public function store(IStorable $storable) {
			$this->usse();
			if ($storable->isDirty() === false) {
				return $this;
			}
			$schema = $storable->getSchema();
			$selectQuery = new SelectQuery();
			foreach ($storable->getIdentifierList() as $value) {
				$property = $value->getProperty();
				$selectQuery->select()
					->count($property->getName(), null)
					->where()
					->eq()
					->property($property->getName())
					->parameter($value->get());
			}
			$selectQuery->from()
				->source($schema->getSchemaName());
			foreach ($this->execute($selectQuery) as $count) {
				break;
			}
			$name = InsertQuery::class;
			if (((int)reset($count)) > 0) {
				$name = UpdateQuery::class;
			}
			$source = [];
			foreach ($storable->getDirtyList() as $value) {
				$property = $value->getProperty();
				$source[$property->getName()] = $value->get();
			}
			$this->driver->execute(new $name($schema, $source));
			return $this;
		}

		public function execute(IQuery $query) {
			$this->usse();
			return $this->driver->execute($query);
		}

		public function collection(IQuery $query) {
		}

		protected function prepare() {
			$this->cache = $this->cacheFactory->factory(static::class);
			$this->sourceNodeQuery = new NodeQuery('/**/source');
		}
	}

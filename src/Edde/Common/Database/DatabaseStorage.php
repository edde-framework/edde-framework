<?php
	declare(strict_types = 1);

	namespace Edde\Common\Database;

	use Edde\Api\Cache\ICache;
	use Edde\Api\Cache\ICacheFactory;
	use Edde\Api\Crate\ICrate;
	use Edde\Api\Database\DriverException;
	use Edde\Api\Database\IDatabaseStorage;
	use Edde\Api\Database\IDriver;
	use Edde\Api\Node\INodeQuery;
	use Edde\Api\Query\IQuery;
	use Edde\Api\Query\IStaticQuery;
	use Edde\Api\Storage\IStorage;
	use Edde\Api\Storage\StorageException;
	use Edde\Common\Node\NodeQuery;
	use Edde\Common\Query\Insert\InsertQuery;
	use Edde\Common\Query\Select\SelectQuery;
	use Edde\Common\Query\Update\UpdateQuery;
	use Edde\Common\Storage\AbstractStorage;
	use PDOException;

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
		 * @var int
		 */
		protected $transaction = 0;

		/**
		 * @param IDriver $driver
		 * @param ICacheFactory $cacheFactory
		 */
		public function __construct(IDriver $driver, ICacheFactory $cacheFactory) {
			$this->driver = $driver;
			$this->cacheFactory = $cacheFactory;
			$this->transaction = 0;
		}

		public function start(bool $exclusive = false): IStorage {
			$this->use();
			if ($this->transaction++ > 0) {
				if ($exclusive === false) {
					return $this;
				}
				throw new StorageException('Cannot start exclusive transaction, there is already running another one.');
			}
			$this->driver->start();
			return $this;
		}

		public function commit(): IStorage {
			$this->use();
			if (--$this->transaction <= 0) {
				$this->driver->commit();
			}
			return $this;
		}

		public function rollback(): IStorage {
			$this->use();
			if ($this->transaction === 0) {
				return $this;
			}
			$this->transaction = 0;
			$this->driver->rollback();
			return $this;
		}

		public function store(ICrate $crate): IStorage {
			$this->use();
			$schema = $crate->getSchema();
			if ($schema->getMeta('storable', false) === false) {
				throw new StorageException(sprintf('Crate [%s] is not marked as storable (in meta data).', $schema->getSchemaName()));
			}
			$crate->update();
			if ($crate->isDirty() === false) {
				return $this;
			}
			$schema = $crate->getSchema();
			$selectQuery = new SelectQuery();
			foreach ($crate->getIdentifierList() as $property) {
				$schemaProperty = $property->getSchemaProperty();
				$selectQuery->select()
					->count($schemaProperty->getName(), null)
					->where()
					->eq()
					->property($schemaProperty->getName())
					->parameter($property->get());
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
			foreach ($crate->getDirtyList() as $property) {
				$schemaProperty = $property->getSchemaProperty();
				$source[$schemaProperty->getName()] = $property->get();
			}
			$this->execute(new $name($schema, $source));
			return $this;
		}

		public function execute(IQuery $query) {
			$this->use();
			try {
				return $this->driver->execute($query);
			} catch (PDOException $e) {
				throw new DriverException(sprintf('Driver [%s] execution failed: %s.', get_class($this->driver), $e->getMessage()), 0, $e);
			}
		}

		public function native(IStaticQuery $staticQuery) {
			$this->use();
			try {
				return $this->driver->native($staticQuery);
			} catch (PDOException $e) {
				throw new DriverException(sprintf('Driver [%s] execution failed: %s.', get_class($this->driver), $e->getMessage()), 0, $e);
			}
		}

		protected function prepare() {
			$this->cache = $this->cacheFactory->factory(static::class);
			$this->sourceNodeQuery = new NodeQuery('/**/source');
		}
	}

<?php
	declare(strict_types = 1);

	namespace Edde\Common\Database;

	use Edde\Api\Cache\ICache;
	use Edde\Api\Cache\ICacheFactory;
	use Edde\Api\Container\IContainer;
	use Edde\Api\Crate\ICrate;
	use Edde\Api\Database\DriverException;
	use Edde\Api\Database\IDatabaseStorage;
	use Edde\Api\Database\IDriver;
	use Edde\Api\Node\INodeQuery;
	use Edde\Api\Query\IQuery;
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
		 * @var bool
		 */
		protected $transaction;

		/**
		 * @param IContainer $container
		 * @param IDriver $driver
		 * @param ICacheFactory $cacheFactory
		 */
		public function __construct(IContainer $container, IDriver $driver, ICacheFactory $cacheFactory) {
			parent::__construct($container);
			$this->driver = $driver;
			$this->cacheFactory = $cacheFactory;
			$this->transaction = false;
		}

		public function start($exclusive = false) {
			$this->use();
			if ($this->transaction && $exclusive) {
				throw new StorageException('Cannot start exclusive transaction, there is already running another one.');
			}
			$this->driver->start();
			return $this;
		}

		public function commit() {
			$this->use();
			$this->transaction = false;
			$this->driver->commit();
			return $this;
		}

		public function rollback() {
			$this->use();
			$this->transaction = false;
			$this->driver->rollback();
			return $this;
		}

		public function store(ICrate $crate) {
			$this->use();
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

		protected function prepare() {
			$this->cache = $this->cacheFactory->factory(static::class);
			$this->sourceNodeQuery = new NodeQuery('/**/source');
		}
	}

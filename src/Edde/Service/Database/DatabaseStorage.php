<?php
	declare(strict_types=1);

	namespace Edde\Service\Database;

	use Edde\Api\Cache\ICache;
	use Edde\Api\Container\IConfigurable;
	use Edde\Api\Crate\ICrate;
	use Edde\Api\Database\DriverException;
	use Edde\Api\Database\IDatabaseStorage;
	use Edde\Api\Database\IDriver;
	use Edde\Api\Database\LazyDriverTrait;
	use Edde\Api\Node\INodeQuery;
	use Edde\Api\Query\IQuery;
	use Edde\Api\Query\IStaticQuery;
	use Edde\Api\Storage\IStorage;
	use Edde\Api\Storage\StorageException;
	use Edde\Common\Container\ConfigurableTrait;
	use Edde\Common\Node\NodeQuery;
	use Edde\Common\Query\Insert\InsertQuery;
	use Edde\Common\Query\Select\SelectQuery;
	use Edde\Common\Query\Update\UpdateQuery;
	use Edde\Common\Storage\AbstractStorage;
	use PDOException;

	/**
	 * Database (persistant) storage implementation.
	 */
	class DatabaseStorage extends AbstractStorage implements IDatabaseStorage, IConfigurable {
		use LazyDriverTrait;
		use ConfigurableTrait;
		/**
		 * @var IDriver
		 */
		protected $driver;
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
		 * @inheritdoc
		 * @throws StorageException
		 */
		public function start(bool $exclusive = false): IStorage {
			if ($this->transaction++ > 0) {
				if ($exclusive === false) {
					return $this;
				}
				throw new StorageException('Cannot start exclusive transaction, there is already running another one.');
			}
			$this->driver->start();
			return $this;
		}

		/**
		 * @inheritdoc
		 */
		public function commit(): IStorage {
			if (--$this->transaction <= 0) {
				$this->driver->commit();
			}
			return $this;
		}

		/**
		 * @inheritdoc
		 */
		public function rollback(): IStorage {
			if ($this->transaction === 0) {
				return $this;
			}
			$this->transaction = 0;
			$this->driver->rollback();
			return $this;
		}

		/**
		 * @inheritdoc
		 * @throws DriverException
		 * @throws StorageException
		 */
		public function store(ICrate $crate): IStorage {
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
			$identifierList = [];
			foreach ($crate->getIdentifierList() as $property) {
				$schemaProperty = $property->getSchemaProperty();
				$selectQuery->select()
					->count($schemaProperty->getName(), null)
					->where()
					->eq()
					->property($schemaProperty->getName())
					->parameter($value = $property->get());
				$identifierList[$schemaProperty->getName()] = $value;
			}
			$selectQuery->from()
				->source($schema->getSchemaName());
			/** @noinspection ForeachSourceInspection */
			/** @noinspection LoopWhichDoesNotLoopInspection */
			foreach ($this->execute($selectQuery) as $count) {
				break;
			}
			$source = [];
			foreach ($crate->getDirtyList() as $property) {
				$schemaProperty = $property->getSchemaProperty();
				$source[$schemaProperty->getName()] = $property->get();
			}
			$query = new InsertQuery($schema, $source);
			if (((int)reset($count)) > 0) {
				$query = new UpdateQuery($schema, $source);
				$where = $query->where();
				foreach ($identifierList as $name => $value) {
					$where->eq()
						->property($name)
						->parameter($value);
				}
			}
			$this->execute($query);
			return $this;
		}

		/**
		 * @inheritdoc
		 * @throws DriverException
		 */
		public function execute(IQuery $query) {
			try {
				$this->driver->setup();
				return $this->driver->execute($query);
			} catch (PDOException $e) {
				throw new DriverException(sprintf('Driver [%s] execution failed: %s.', get_class($this->driver), $e->getMessage()), 0, $e);
			}
		}

		/**
		 * @inheritdoc
		 * @throws DriverException
		 */
		public function native(IStaticQuery $staticQuery) {
			try {
				$this->driver->setup();
				return $this->driver->native($staticQuery);
			} catch (PDOException $e) {
				throw new DriverException(sprintf('Driver [%s] execution failed: %s.', get_class($this->driver), $e->getMessage()), 0, $e);
			}
		}

		/**
		 * @inheritdoc
		 */
		protected function handleInit() {
			$this->sourceNodeQuery = new NodeQuery('/**/source');
			$this->transaction = 0;
		}
	}

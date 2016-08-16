<?php
	declare(strict_types = 1);

	namespace Edde\Ext\Database\Sqlite;

	use Edde\Api\Database\DriverException;
	use Edde\Api\Query\IQuery;
	use Edde\Api\Query\IStaticQueryFactory;
	use Edde\Common\Database\AbstractDriver;
	use PDO;
	use PDOStatement;

	class SqliteDriver extends AbstractDriver {
		/**
		 * @var PDO
		 */
		public $pdo;
		/**
		 * @var string
		 */
		protected $dsn;
		/**
		 * @var IStaticQueryFactory
		 */
		protected $staticQueryFactory;
		/**
		 * @var PDOStatement[]
		 */
		protected $statementList = [];

		/**
		 * @param string $dsn
		 */
		public function __construct($dsn) {
			$this->dsn = $dsn;
		}

		public function start($exclusive = false) {
			$this->use();
			$this->pdo->beginTransaction();
			return $this;
		}

		public function commit() {
			$this->use();
			$this->pdo->commit();
			return $this;
		}

		public function rollback() {
			$this->use();
			$this->pdo->rollBack();
			return $this;
		}

		public function delimite($delimite) {
			return '"' . str_replace('"', '""', $delimite) . '"';
		}

		public function quote($quote) {
			$this->use();
			return $this->pdo->quote($quote);
		}

		public function type($type) {
			$this->use();
			if (isset($this->typeList[$type]) === false) {
				throw new DriverException(sprintf('Unknown type [%s] for driver [%s].', $type, static::class));
			}
			return $this->typeList[$type];
		}

		public function execute(IQuery $query) {
			$this->use();
			$staticQuery = $this->staticQueryFactory->create($query);
			if (isset($this->statementList[$sql = $staticQuery->getQuery()]) === false) {
				$this->statementList[$sql] = $statement = $this->pdo->prepare($sql);
				$statement->setFetchMode(PDO::FETCH_ASSOC);
			}
			$statement = $this->statementList[$sql];
			$statement->execute($staticQuery->getParameterList());
			return $statement;
		}

		public function close() {
			$this->pdo = null;
			$this->statementList = [];
			return $this;
		}

		protected function prepare() {
			if (extension_loaded('pdo_sqlite') === false) {
				throw new DriverException('Sqlite PDO is not available, oops!');
			}
			$this->pdo = new PDO($this->dsn);
			$this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			$this->pdo->setAttribute(PDO::ATTR_STRINGIFY_FETCHES, false);
			$this->pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
			$this->pdo->setAttribute(PDO::ATTR_CASE, PDO::CASE_NATURAL);
			$this->setTypeList([
				null => 'TEXT',
				'int' => 'INTEGER',
				'float' => 'FLOAT',
				'long' => 'INTEGER',
				'string' => 'TEXT',
				'text' => 'TEXT',
				'datetime' => 'TIMESTAMP',
			]);
			$this->staticQueryFactory = new SqliteQueryFactory($this);
		}
	}

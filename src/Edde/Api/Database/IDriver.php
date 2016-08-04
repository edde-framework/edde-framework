<?php
	declare(strict_types = 1);

	namespace Edde\Api\Database;

	use Edde\Api\Query\IQuery;
	use Edde\Api\Usable\IUsable;
	use PDOStatement;

	/**
	 * Custom driver per database engine.
	 */
	interface IDriver extends IUsable {
		/**
		 * start a transaction
		 *
		 * @param bool $exclusive if true and there is already transaction, exception should be thrown
		 *
		 * @return $this
		 */
		public function start($exclusive = false);

		/**
		 * commit a transaciton
		 *
		 * @return $this
		 */
		public function commit();

		/**
		 * rollback a transaction
		 *
		 * @return $this
		 */
		public function rollback();

		/**
		 * @param string $delimite
		 *
		 * @return string
		 */
		public function delimite($delimite);

		/**
		 * @param string $quote
		 *
		 * @return string
		 */
		public function quote($quote);

		/**
		 * translate common (php) type to the database type (e.g. bool to int, ...)
		 *
		 * @param string $type
		 *
		 * @return string
		 */
		public function type($type);

		/**
		 * @param IQuery $query
		 *
		 * @return PDOStatement
		 */
		public function execute(IQuery $query);
	}

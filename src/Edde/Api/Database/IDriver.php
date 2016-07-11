<?php
	namespace Edde\Api\Database;

	use Edde\Api\Query\IQuery;
	use Edde\Api\Usable\IUsable;
	use PDOStatement;

	/**
	 * Custom driver per database engine.
	 */
	interface IDriver extends IUsable {
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

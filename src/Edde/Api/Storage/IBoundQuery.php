<?php
	declare(strict_types = 1);

	namespace Edde\Api\Storage;

	use Edde\Api\Query\IQuery;

	/**
	 * Special case of a Query bound to particular storage.
	 */
	interface IBoundQuery extends IQuery {
		/**
		 * bind query to the given storage
		 *
		 * @param IStorage $storage
		 *
		 * @return IBoundQuery
		 */
		public function bind(IStorage $storage): IBoundQuery;

		/**
		 * execute method of a storage
		 *
		 * @return mixed
		 */
		public function execute();
	}

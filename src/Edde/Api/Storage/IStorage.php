<?php
	declare(strict_types = 1);

	namespace Edde\Api\Storage;

	use Edde\Api\Crate\ICrate;
	use Edde\Api\Query\IQuery;
	use Edde\Api\Usable\IUsable;

	/**
	 * This is abstracted way how to store (serialize) almost any object; storage can be arbitrary technology with ability to understand Edde's IQL.
	 */
	interface IStorage extends IUsable {
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
		 * execute the given query against this storage and return storage's native result
		 *
		 * @param IQuery $query
		 *
		 * @return mixed
		 */
		public function execute(IQuery $query);

		/**
		 * try to store the given crate
		 *
		 * @param ICrate $crate
		 *
		 * @return $this
		 */
		public function store(ICrate $crate);

		/**
		 * return collection based on the input query; if storage doesn't understand the queery, exception should be thrown
		 *
		 * @param string $crate of Crate
		 * @param IQuery $query
		 * @param string $schema
		 *
		 * @return ICrate[]|ICollection
		 */
		public function collection(string $crate, IQuery $query = null, string $schema = null): ICollection;

		/**
		 * helper method for a m:n crate collection
		 *
		 * @param ICrate $crate
		 * @param string $relation
		 * @param string $source
		 * @param string $target
		 * @param string $crateTo optional target crate class
		 *
		 * @return \Edde\Api\Crate\ICrate[]|ICollection
		 */
		public function collectionTo(ICrate $crate, string $relation, string $source, string $target, string $crateTo = null): ICollection;

		/**
		 * retrieve crate by the given query; it should formally go through a collection method; if there is no such crate, exception should be thrown
		 *
		 * @param string $crate
		 * @param IQuery $query
		 * @param string $schema
		 *
		 * @return ICrate
		 */
		public function load(string $crate, IQuery $query, string $schema = null);
	}

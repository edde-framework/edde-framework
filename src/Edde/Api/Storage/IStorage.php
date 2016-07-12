<?php
	namespace Edde\Api\Storage;

	use Edde\Api\Query\IQuery;
	use Edde\Api\Schema\ISchema;
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
		 * try to store the given storable
		 *
		 * @param IStorable $storable
		 *
		 * @return $this
		 */
		public function store(IStorable $storable);

		/**
		 * return collection based on the input query; if storage doesn't understand the queery, exception should be thrown
		 *
		 * @param ISchema $schema of Storables
		 * @param IQuery $query
		 *
		 * @return ICollection|IStorable[]
		 */
		public function collection(ISchema $schema, IQuery $query);

		/**
		 * retrieve storable by the given query; it should formally go through a collection method; if there is no such storable, exception should be thrown
		 *
		 * @param ISchema $schema of requested storable
		 * @param IQuery $query
		 *
		 * @return IStorable
		 */
		public function storable(ISchema $schema, IQuery $query);
	}

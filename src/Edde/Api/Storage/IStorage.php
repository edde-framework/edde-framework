<?php
	namespace Edde\Api\Storage;

	use Edde\Api\Query\IQuery;
	use Edde\Api\Usable\IUsable;

	/**
	 * This is abstracted way how to store (serialize) almost any object; storage can be arbitrary technology with ability to understand Edde's IQL.
	 */
	interface IStorage extends IUsable {
		/**
		 * execute the given query against this storage
		 *
		 * @param IQuery $query
		 *
		 * @return ICollectionIterator
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
		 * @param IQuery $query
		 *
		 * @return ICollection|IStorable[]
		 *
		 * @throws StorageException
		 */
		public function collection(IQuery $query);

		/**
		 * retrieve storable by the given query; it should formally go through a collection method; if there is no such storable, exception should be thrown
		 *
		 * @param IQuery $query
		 *
		 * @return IStorable
		 *
		 * @throws StorageException
		 */
		public function storable(IQuery $query);
	}

<?php
	namespace Edde\Api\Storage;

	use Edde\Api\Query\IQuery;

	/**
	 * Collection of IStorable; default iteration should execute iteration over all available storables in this collection.
	 */
	interface ICollection extends ICollectionIterator {
		/**
		 * save the given storable to the storage bound to this collection; in general any storable can be saved in any collection, formally
		 * unknown (mismatched schemas) should throw an exception
		 *
		 * @param IStorable $storable
		 *
		 * @return $this
		 */
		public function store(IStorable $storable);

		/**
		 * execute the query on this collection and return collection iterator with results; when the query has no results, iterator
		 * will be empty
		 *
		 * @param IQuery $query
		 *
		 * @return ICollectionIterator
		 */
		public function query(IQuery $query);
	}

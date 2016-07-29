<?php
	namespace Edde\Api\Crate;

	use Edde\Api\Schema\ISchema;
	use IteratorAggregate;

	/**
	 * Collection of crates created on demand.
	 */
	interface ICollection extends IteratorAggregate {
		/**
		 * return schema of this collection
		 *
		 * @return ISchema
		 */
		public function getSchema();

		/**
		 * create a new crate; the crate should not be added to a collection
		 *
		 * @return ICrate
		 */
		public function createCrate();

		/**
		 * add crate to this collection
		 *
		 * @param ICrate $crate
		 *
		 * @return $this
		 */
		public function addCrate(ICrate $crate);
	}

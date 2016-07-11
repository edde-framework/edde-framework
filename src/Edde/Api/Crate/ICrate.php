<?php
	namespace Edde\Api\Crate;

	use Edde\Api\Schema\ISchema;

	/**
	 * General object which is used to describe relations between objects (not necesarilly database objects) and
	 * theirs hierarchy.
	 */
	interface ICrate extends IValueSet {
		/**
		 * return a schema of this storable
		 *
		 * @return ISchema
		 */
		public function getSchema();

		/**
		 * @return IValue[]
		 */
		public function getValueList();
	}

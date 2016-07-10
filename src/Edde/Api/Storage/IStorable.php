<?php
	namespace Edde\Api\Storage;

	use Edde\Api\Schema\ISchema;

	/**
	 * Every storable object must be formally marked by this interface.
	 */
	interface IStorable {
		/**
		 * return a schema of this storable
		 *
		 * @return ISchema
		 */
		public function getSchema();
	}

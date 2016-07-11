<?php
	namespace Edde\Api\Resource;

	/**
	 * Interface for simple resource querying.
	 */
	interface IResourceQuery {
		/**
		 * query resource by a name
		 *
		 * @param string $name
		 *
		 * @return $this
		 */
		public function name($name);
	}

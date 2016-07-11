<?php
	namespace Edde\Api\Resource;

	use Edde\Api\Query\IQuery;

	/**
	 * Interface for simple resource querying.
	 */
	interface IResourceQuery {
		/**
		 * return current resource query
		 *
		 * @return IQuery
		 */
		public function getQuery();

		/**
		 * query resource by a name
		 *
		 * @param string $name
		 *
		 * @return $this
		 */
		public function name($name);
	}

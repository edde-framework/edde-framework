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

		/**
		 * @param string $name
		 *
		 * @return $this
		 */
		public function nameLike($name);

		/**
		 * @param string $url
		 *
		 * @return $this
		 */
		public function url($url);

		/**
		 * @param string $url
		 *
		 * @return $this
		 */
		public function urlLike($url);

		/**
		 * execute query and retrieve IResource
		 *
		 * @return IResource
		 */
		public function resource();

		/**
		 * retrieve iterator/collection of IResource
		 *
		 * @return IResource[]
		 */
		public function collection();

		/**
		 * check if the current query has some results
		 *
		 * @return bool
		 */
		public function hasResource();
	}

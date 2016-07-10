<?php
	namespace Edde\Api\Cache;

	/**
	 * Cache invalidator interface; it is used when retrieving value to check if given node is still valid.
	 */
	interface IInvalidator {
		/**
		 * @param ICacheNode $cacheNode
		 *
		 * @return bool
		 */
		public function isValid(ICacheNode $cacheNode);
	}

<?php
	namespace Edde\Api\Crate;

	/**
	 * This mechanism is inteded to use as conversion from an input data to crates.
	 */
	interface ICrateFactory {
		/**
		 * build crate list from the input array
		 *
		 * @param array $crateList
		 *
		 * @return ICrate[]
		 */
		public function build(array $crateList);
	}

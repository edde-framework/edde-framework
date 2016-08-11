<?php
	declare(strict_types = 1);

	namespace Edde\Api\Crate;

	/**
	 * This mechanism is inteded to use as conversion from an input data to crates.
	 */
	interface ICrateFactory {
		/**
		 * create single crate from input
		 *
		 * @param array $source
		 * @param string $name
		 *
		 * @return ICrate
		 */
		public function crate(array $source, string $name): ICrate;

		/**
		 * return list of same crates
		 *
		 * @param array $sourceList
		 * @param string $name
		 *
		 * @return ICrate[]
		 */
		public function createList(array $sourceList, string $name): array;

		/**
		 * build crate list from the input array
		 *
		 * @param array $crateList
		 *
		 * @return ICrate[]
		 */
		public function build(array $crateList);
	}

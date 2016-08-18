<?php
	declare(strict_types = 1);

	namespace Edde\Api\Crate;

	/**
	 * This mechanism is inteded to use as conversion from an input data to crates.
	 */
	interface ICrateFactory {
		/**
		 * create crate with a given class (should be through container) and with the given schema
		 *
		 * @param string $crate
		 * @param array $push
		 * @param string $schema
		 *
		 * @return ICrate
		 */
		public function crate(string $crate, array $push = null, string $schema = null): ICrate;

		/**
		 * create crate collection
		 *
		 * @param string $schema
		 * @param string $crate
		 *
		 * @return ICollection
		 */
		public function collection(string $schema, string $crate = null): ICollection;

		/**
		 * build crate list from the input array
		 *
		 * @param array $crateList
		 *
		 * @return ICrate[]
		 */
		public function build(array $crateList);
	}

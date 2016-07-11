<?php
	namespace Edde\Api\Crate;

	interface ICrateFactory {
		/**
		 * create a new crate; this method should call IContainer::create and only verify, if returned object is ICrate
		 *
		 * @param string $name
		 * @param array ...$parameterList
		 *
		 * @return ICrate
		 */
		public function create($name, ...$parameterList);

		/**
		 * prepare crate for usage; fill with properties
		 *
		 * @param ICrate $crate
		 *
		 * @return $this
		 */
		public function fill(ICrate $crate);
	}

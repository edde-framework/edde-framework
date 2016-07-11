<?php
	namespace Edde\Api\Storage;

	interface IStorableFactory {
		/**
		 * create storable by the given name (IContainer::create() should be called)
		 *
		 * @param string $name
		 * @param array ...$parameterList
		 *
		 * @return IStorable
		 */
		public function create($name, ...$parameterList);
	}

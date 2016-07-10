<?php
	namespace Edde\Api\Container;

	use Edde\Api\Node\IAbstractNode;

	/**
	 * Class dependency item.
	 */
	interface IDependency extends IAbstractNode {
		/**
		 * @return string
		 */
		public function getName();

		/**
		 * return dependency list of this dependency
		 *
		 * @return IDependency[]
		 */
		public function getDependencyList();
	}

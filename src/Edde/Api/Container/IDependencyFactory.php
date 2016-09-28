<?php
	declare(strict_types = 1);

	namespace Edde\Api\Container;

	use Edde\Api\Deffered\IDeffered;

	/**
	 * Interface for factories building dependency tree for a class.
	 */
	interface IDependencyFactory extends IDeffered {
		/**
		 * build dependency tree for a given name
		 *
		 * @param string $name
		 *
		 * @return IDependency
		 */
		public function create(string $name): IDependency;
	}

<?php
	declare(strict_types = 1);

	namespace Edde\Api\Container;

	use Edde\Api\Usable\IUsable;

	/**
	 * Interface for factories building dependency tree for a class.
	 */
	interface IDependencyFactory extends IUsable {
		/**
		 * build dependency tree for a given name
		 *
		 * @param string $name
		 *
		 * @return IDependency
		 */
		public function create($name);
	}

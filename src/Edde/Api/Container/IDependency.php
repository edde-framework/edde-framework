<?php
	declare(strict_types = 1);

	namespace Edde\Api\Container;

	use Edde\Api\Node\INode;

	/**
	 * Class dependency item.
	 */
	interface IDependency extends INode {
		/**
		 * @return bool
		 */
		public function hasClass(): bool;

		/**
		 * return dependency class or null
		 *
		 * @return string|null
		 */
		public function getClass();

		/**
		 * @return bool
		 */
		public function isOptional(): bool;

		/**
		 * return dependency list of this dependency
		 *
		 * @return IDependency[]
		 */
		public function getDependencyList(): array;
	}

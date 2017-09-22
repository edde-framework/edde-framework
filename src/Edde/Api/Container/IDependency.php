<?php
	declare(strict_types=1);

	namespace Edde\Api\Container;

	/**
	 * Describes dependency from point of view of object (or closure); so dependency is "me".
	 */
	interface IDependency {
		/**
		 * get list of mandatory parameters
		 *
		 * @return IParameter[]
		 */
		public function getParameterList(): array;

		/**
		 * get list of injectable parameters
		 *
		 * @return IParameter[]
		 */
		public function getInjectList(): array;

		/**
		 * get list of lazy parameters
		 *
		 * @return IParameter[]
		 */
		public function getLazyList(): array;

		/**
		 * return list of configurator names
		 *
		 * @return string[]
		 */
		public function getConfiguratorList(): array;
	}

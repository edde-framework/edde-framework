<?php
	declare(strict_types = 1);

	namespace Edde\Api\Container;

	/**
	 * Marker interface for classes supporting external configuration.
	 */
	interface IConfigurable {
		/**
		 * register set of config handlers
		 *
		 * @param IConfigHandler[] $configHandlerList
		 *
		 * @return $this
		 */
		public function registerConfigHandlerList(array $configHandlerList);

		/**
		 * execute object configuration (so after this method object should be fully prepared for use)
		 *
		 * @return $this
		 */
		public function config();
	}

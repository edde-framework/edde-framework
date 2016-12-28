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
		 * this method should be called after all dependencies are available; also there should NOT be any heavy computations, only lightweight
		 * simple stuff
		 */
		public function init();

		/**
		 * @return bool
		 */
		public function isInitialized(): bool;

		/**
		 * execute object configuration (so after this method object should be fully prepared for use)
		 *
		 * @return $this
		 */
		public function config();

		/**
		 * what to say here, hmm ;)? If method config() has been called, this is true
		 *
		 * @return bool
		 */
		public function isConfigured(): bool;
	}

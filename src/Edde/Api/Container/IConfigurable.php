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
		 * this method should be called after all dependencies are
		 * available; also there should NOT be any heavy computations, only
		 * lightweight simple stuff
		 *
		 * @param bool $force
		 */
		public function init(bool $force = false);

		/**
		 * @return bool
		 */
		public function isInitialized(): bool;

		/**
		 * execute object initialization; object must be serializable after this method
		 *
		 * @param bool $force
		 */
		public function warmup(bool $force = false);

		/**
		 * @return bool
		 */
		public function isWarmedup(): bool;

		/**
		 * execute object configuration (so after this method object should be fully prepared for use)
		 *
		 * @param bool $force
		 */
		public function config(bool $force = false);

		/**
		 * what to say here, hmm ;)? If method config() has been called, this is true
		 *
		 * @return bool
		 */
		public function isConfigured(): bool;

		/**
		 * do any heavy computations; after this object is usualy not serializable
		 *
		 * @param bool $force
		 */
		public function setup(bool $force = false);

		/**
		 * has benn object set up?
		 */
		public function isSetup(): bool;
	}

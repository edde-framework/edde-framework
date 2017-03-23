<?php
	declare(strict_types=1);

	namespace Edde\Api\Config;

	/**
	 * Marker interface for classes supporting external configuration.
	 */
	interface IConfigurable {
		/**
		 * register configurator
		 *
		 * @param IConfigurator $configurator
		 *
		 * @return $this
		 */
		public function configurator(IConfigurator $configurator);

		/**
		 * register set of config handlers
		 *
		 * @param IConfigurator[] $configuratorList
		 *
		 * @return $this
		 */
		public function registerConfiguratorList(array $configuratorList);

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
		 * throws an exception if configurable has not been initialized
		 *
		 * @return IConfigurable
		 */
		public function checkInit();

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
		 * check if configurable has been warmedup
		 *
		 * @return IConfigurable
		 */
		public function checkWarmup();

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
		 * check if configurable has been configured
		 *
		 * @return IConfigurable
		 */
		public function checkConfig();

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

		/**
		 * check if configurable has been setup
		 *
		 * @return IConfigurable
		 */
		public function checkSetup();
	}

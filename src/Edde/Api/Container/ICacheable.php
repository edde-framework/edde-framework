<?php
	declare(strict_types = 1);

	namespace Edde\Api\Container;

	/**
	 * Marker interface for instances which can be cached from container (thus supporting dependency tree serialization).
	 */
	interface ICacheable {
		/**
		 * when instance is deserialized, this method should be called
		 *
		 * @param IContainer $container
		 * @param string     $cache
		 *
		 * @return
		 */
		static public function warmup(IContainer $container, string $cache);

		/**
		 * method called when object is falling asleep (before serialization)
		 *
		 * @param IContainer $container
		 */
		public function sleep(IContainer $container): string;
	}

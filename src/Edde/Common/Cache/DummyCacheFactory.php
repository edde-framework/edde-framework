<?php
	declare(strict_types = 1);

	namespace Edde\Common\Cache;

	use Edde\Ext\Cache\DevNullCacheStorage;

	class DummyCacheFactory extends CacheFactory {
		public function __construct() {
			parent::__construct(__DIR__, new DevNullCacheStorage());
		}
	}

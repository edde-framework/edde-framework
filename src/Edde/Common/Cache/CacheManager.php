<?php
	declare(strict_types = 1);

	namespace Edde\Common\Cache;

	use Edde\Api\Cache\ICacheable;

	/**
	 * Formal cache cache without any dark magic. You must provide namespace (for example application version
	 * for simple cache invalidation).
	 */
	class CacheManager extends AbstractCacheManager implements ICacheable {
	}

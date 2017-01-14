<?php
	declare(strict_types = 1);

	namespace Edde\Common\Cache;

	use Edde\Api\Cache\ICacheable;

	/**
	 * Cache class is wrapper around low-level ICacheStorage. It is intended to use new instance per caching scope.
	 */
	class Cache extends AbstractCache implements ICacheable {
	}

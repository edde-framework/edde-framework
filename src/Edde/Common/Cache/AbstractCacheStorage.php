<?php
	declare(strict_types = 1);

	namespace Edde\Common\Cache;

	use Edde\Api\Cache\ICacheStorage;
	use Edde\Common\Deffered\AbstractDeffered;

	/**
	 * Common stuff for cache storage implementation.
	 */
	abstract class AbstractCacheStorage extends AbstractDeffered implements ICacheStorage {
	}

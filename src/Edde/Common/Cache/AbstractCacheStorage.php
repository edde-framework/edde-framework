<?php
	declare(strict_types = 1);

	namespace Edde\Common\Cache;

	use Edde\Api\Cache\ICacheStorage;
	use Edde\Common\Usable\AbstractUsable;

	abstract class AbstractCacheStorage extends AbstractUsable implements ICacheStorage {
	}

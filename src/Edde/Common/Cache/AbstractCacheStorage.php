<?php
	declare(strict_types = 1);

	namespace Edde\Common\Cache;

	use Edde\Api\Cache\ICacheStorage;
	use Edde\Common\Deffered\AbstractDeffered;

	abstract class AbstractCacheStorage extends AbstractDeffered implements ICacheStorage {
	}

<?php
	declare(strict_types = 1);

	use Edde\Api\Application\IErrorControl;
	use Edde\Api\Cache\ICacheStorage;
	use Edde\Ext\Application\RethrowErrorControl;
	use Edde\Ext\Cache\DevNullCacheStorage;

	return [
		IErrorControl::class => RethrowErrorControl::class,
		ICacheStorage::class => new DevNullCacheStorage(),
	];

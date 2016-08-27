<?php
	declare(strict_types = 1);

	use Edde\Api\Application\IErrorControl;
	use Edde\Api\Cache\ICacheFactory;
	use Edde\Common\Cache\DummyCacheFactory;
	use Edde\Ext\Application\RethrowErrorControl;

	return [
		IErrorControl::class => RethrowErrorControl::class,
		ICacheFactory::class => new DummyCacheFactory(),
	];

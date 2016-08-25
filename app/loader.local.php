<?php
	declare(strict_types = 1);

	use Edde\Api\Application\IErrorControl;
	use Edde\Ext\Application\RethrowErrorControl;

	return [
		IErrorControl::class => RethrowErrorControl::class,
	];

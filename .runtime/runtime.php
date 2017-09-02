<?php
	declare(strict_types=1);

	use Edde\Api\Container\IContainer;

	/** @var $container IContainer */
	$container = require __DIR__ . '/loader.php';
	$container->call(function () {
	}, [], basename(__FILE__));

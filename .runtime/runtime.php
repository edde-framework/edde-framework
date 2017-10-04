<?php
	declare(strict_types=1);

	use Edde\Api\Container\IContainer;

	/** @var $container IContainer */
	$container = require __DIR__ . '/loader.php';
	/**
	 * there is an one magical factory bound to IApplication::run method; this factory
	 * creates IApplication and executes it's run method
	 */
	//	exit($container->create('run', [], basename(__FILE__)));

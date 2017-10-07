<?php
	declare(strict_types=1);
	use Edde\Api\Container\IContainer;
	use Tracy\Debugger;

	try {
		/**
		 * there is an one magical factory bound to IApplication::run method; this factory
		 * creates IApplication and executes it's run method
		 */
		/** @var $container IContainer */
		$container = require __DIR__ . '/loader.php';
		exit($container->create('run', [], basename(__FILE__)));
	} catch (\Throwable $e) {
		Debugger::log($e);
		die(sprintf('Critical application Exception [%s]; see logs.', get_class($e)));
	}

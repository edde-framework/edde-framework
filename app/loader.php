<?php
	use Edde\Api\Application\IApplication;
	use Edde\Api\Database\IDriver;
	use Edde\Api\File\IRootDirectory;
	use Edde\Common\File\RootDirectory;
	use Edde\Common\Runtime\Runtime;
	use Edde\Ext\Database\Sqlite\SqliteDriver;
	use Edde\Ext\Runtime\DefaultSetupHandler;
	use Tracy\Debugger;

	require_once(__DIR__ . '/vendor/autoload.php');
	require_once(__DIR__ . '/../src/loader.php');

	Debugger::enable(Debugger::DEVELOPMENT, __DIR__ . '/logs');
	Debugger::$strictMode = true;

	$factoryList = [
		IRootDirectory::class => new RootDirectory(__DIR__),
		IDriver::class => function () {
			return new SqliteDriver('sqlite:' . __DIR__ . '/application.sqlite');
		},
	];
//	$cacheFactory = new CacheFactory(__DIR__, new FileCacheStorage(new CacheDirectory(__DIR__ . '/temp/cache')));
	$cacheFactory = null;
	Runtime::execute(DefaultSetupHandler::create($cacheFactory, $factoryList), function (IApplication $application) {
		$application->run();
	});

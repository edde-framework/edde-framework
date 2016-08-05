<?php
	declare(strict_types = 1);

	use App\AppSetupHandler;
	use Edde\Api\Application\IApplication;
	use Edde\Api\Cache\ICacheStorage;
	use Edde\Api\File\IRootDirectory;
	use Edde\Common\Cache\CacheDirectory;
	use Edde\Common\Cache\CacheFactory;
	use Edde\Common\File\RootDirectory;
	use Edde\Common\Runtime\Runtime;
	use Edde\Ext\Cache\FileCacheStorage;
	use Tracy\Debugger;

	require_once(__DIR__ . '/vendor/autoload.php');
	require_once(__DIR__ . '/../src/loader.php');
	require_once(__DIR__ . '/src/loader.php');

	Debugger::enable(Debugger::DEVELOPMENT, __DIR__ . '/logs');
	Debugger::$strictMode = true;

	$factoryList = [
		IRootDirectory::class => new RootDirectory(__DIR__),
	];
	$cacheFactory = new CacheFactory(__DIR__, $factoryList[ICacheStorage::class] = new FileCacheStorage(new CacheDirectory(__DIR__ . '/temp/cache')));
//	$cacheFactory = null;
	Runtime::execute(AppSetupHandler::create($cacheFactory, $factoryList), function (IApplication $application) {
		$application->run();
	});

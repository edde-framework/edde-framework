<?php
	declare(strict_types = 1);

	use App\AppSetupHandler;
	use Edde\Api\Application\IApplication;
	use Edde\Api\Cache\ICacheStorage;
	use Edde\Api\File\IRootDirectory;
	use Edde\Common\Cache\CacheFactory;
	use Edde\Common\File\RootDirectory;
	use Edde\Common\Runtime\Runtime;
	use Edde\Ext\Cache\MemcacheCacheStorage;
	use Tracy\Debugger;

	require_once(__DIR__ . '/vendor/autoload.php');
	require_once(__DIR__ . '/../src/loader.php');
	require_once(__DIR__ . '/src/loader.php');

	Debugger::enable(Debugger::DEVELOPMENT, __DIR__ . '/logs');
	Debugger::$strictMode = true;

	$factoryList = array_merge([
		IRootDirectory::class => new RootDirectory(__DIR__),
		ICacheStorage::class => (new MemcacheCacheStorage())->addServer('127.0.0.1'),
	], is_array($local = include_once(__DIR__ . '/loader.local.php')) ? $local : []);

	Runtime::execute(AppSetupHandler::create(new CacheFactory(__DIR__, $factoryList[ICacheStorage::class]), $factoryList), function (IApplication $application) {
		$application->run();
	});

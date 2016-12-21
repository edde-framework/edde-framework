<?php
	/**
	 * file responsible for requiring all dependencies
	 */
	declare(strict_types = 1);

	use Edde\Api\Application\IApplication;
	use Edde\Api\Application\IRequest;
	use Edde\Api\File\IRootDirectory;
	use Edde\Api\Log\ILogService;
	use Edde\Api\Router\IRouterService;
	use Edde\Common\Application\Application;
	use Edde\Common\File\RootDirectory;
	use Edde\Common\Log\LogService;
	use Edde\Common\Router\RouterService;
	use Edde\Ext\Container\ContainerFactory;
	use Tracy\Debugger;

	require_once __DIR__ . '/lib/autoload.php';
	require_once __DIR__ . '/../src/loader.php';
	require_once __DIR__ . '/src/loader.php';

	Debugger::enable(Debugger::DEVELOPMENT, __DIR__ . '/logs');
	Debugger::$strictMode = true;
	Debugger::$showBar = true;
	Debugger::$onFatalError[] = function ($e) {
		Debugger::log($e);
	};

	/** @noinspection PhpIncludeInspection */
	return ContainerFactory::cache($factoryList = array_merge([
		IRootDirectory::class => new RootDirectory(__DIR__),
		IApplication::class => Application::class,
		ILogService::class => LogService::class,
		IRouterService::class => RouterService::class,
		IRequest::class => IRouterService::class . '::createRequest',
	], is_array($local = @include __DIR__ . '/loader.local.php') ? $local : []), __DIR__ . '/temp/container-' . sha1(implode('', array_keys($factoryList))) . '.cache');

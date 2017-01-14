<?php
	/**
	 * file responsible for requiring all dependencies
	 */
	declare(strict_types = 1);

	use Edde\Api\Converter\IConverterManager;
	use Edde\Api\File\IRootDirectory;
	use Edde\Api\Router\IRouterService;
	use Edde\App\Converter\ConverterManagerConfigHandler;
	use Edde\App\Router\RouterServiceConfigHandler;
	use Edde\Common\File\RootDirectory;
	use Edde\Ext\Container\ClassFactory;
	use Edde\Ext\Container\ContainerFactory;
	use Edde\Framework;
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
	return ContainerFactory::cache($factoryList = array_merge(ContainerFactory::getDefaultFactoryList(), [
		IRootDirectory::class => ContainerFactory::instance(RootDirectory::class, [__DIR__]),
	], is_array($local = @include __DIR__ . '/loader.local.php') ? $local : [], [
		new ClassFactory(),
	]), [
		IRouterService::class => [
			RouterServiceConfigHandler::class,
		],
		IConverterManager::class => [
			ConverterManagerConfigHandler::class,
		],
	], __DIR__ . '/temp/container-' . sha1(implode('', array_keys($factoryList)) . new Framework()) . '.cache');

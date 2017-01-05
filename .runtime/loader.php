<?php
	/**
	 * file responsible for requiring all dependencies
	 */
	declare(strict_types = 1);

	use Edde\Api\Application\IApplication;
	use Edde\Api\Application\IRequest;
	use Edde\Api\Application\IResponseManager;
	use Edde\Api\Cache\ICacheDirectory;
	use Edde\Api\Cache\ICacheStorage;
	use Edde\Api\File\IRootDirectory;
	use Edde\Api\Http\IHeaderFactory;
	use Edde\Api\Http\IHeaderList;
	use Edde\Api\Http\IHttpRequest;
	use Edde\Api\Http\IHttpResponse;
	use Edde\Api\Http\IPostFactory;
	use Edde\Api\Http\IPostList;
	use Edde\Api\Http\IRequestUrl;
	use Edde\Api\Http\IRequestUrlFactory;
	use Edde\Api\Log\ILogService;
	use Edde\Api\Resource\IResourceManager;
	use Edde\Api\Router\IRouterService;
	use Edde\Api\Runtime\IRuntime;
	use Edde\Common\Application\Application;
	use Edde\Common\Application\ResponseManager;
	use Edde\Common\Cache\CacheDirectory;
	use Edde\Common\File\RootDirectory;
	use Edde\Common\Http\HeaderFactory;
	use Edde\Common\Http\HttpRequest;
	use Edde\Common\Http\HttpResponse;
	use Edde\Common\Http\PostFactory;
	use Edde\Common\Http\RequestUrlFactory;
	use Edde\Common\Log\LogService;
	use Edde\Common\Resource\ResourceManager;
	use Edde\Common\Router\RouterService;
	use Edde\Common\Runtime\Runtime;
	use Edde\Ext\Cache\FlatFileCacheStorage;
	use Edde\Ext\Container\ClassFactory;
	use Edde\Ext\Container\ContainerFactory;
	use Edde\Ext\Router\RouterServiceConfigHandler;
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
	return ContainerFactory::cache($factoryList = array_merge([
		IRootDirectory::class => new RootDirectory(__DIR__),
		ICacheDirectory::class => new CacheDirectory(__DIR__ . '/temp/cache'),
		ICacheStorage::class => FlatFileCacheStorage::class,
		IRuntime::class => Runtime::class,
		IHttpResponse::class => HttpResponse::class,
		IApplication::class => Application::class,
		ILogService::class => LogService::class,
		IRouterService::class => RouterService::class,
		IRequest::class => IRouterService::class . '::createRequest',
		IPostFactory::class => PostFactory::class,
		IPostList::class => IPostFactory::class . '::create',
		IRequestUrlFactory::class => RequestUrlFactory::class,
		IRequestUrl::class => IRequestUrlFactory::class . '::create',
		IHeaderFactory::class => HeaderFactory::class,
		IHeaderList::class => IHeaderFactory::class . '::create',
		IHttpRequest::class => HttpRequest::class,
		IResponseManager::class => ResponseManager::class,
		IResourceManager::class => ResourceManager::class,
		new ClassFactory(),
	], is_array($local = @include __DIR__ . '/loader.local.php') ? $local : []), [
		IRouterService::class => [
			RouterServiceConfigHandler::class,
		],
	], __DIR__ . '/temp/container-' . sha1(implode('', array_keys($factoryList)) . new Framework()) . '.cache');

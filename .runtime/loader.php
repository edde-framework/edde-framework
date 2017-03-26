<?php
	/**
	 * file responsible for requiring all dependencies
	 */
	declare(strict_types=1);

	use Edde\Api\Application\IContext;
	use Edde\Api\Application\IRequestQueue;
	use Edde\Api\Cache\ICacheManager;
	use Edde\Api\Converter\IConverterManager;
	use Edde\Api\File\IRootDirectory;
	use Edde\Api\Resource\IResourceManager;
	use Edde\Api\Resource\IResourceProvider;
	use Edde\Api\Router\IRouterService;
	use Edde\Api\Template\ICompiler;
	use Edde\App\Application\AppContext;
	use Edde\App\Converter\ConverterManagerConfigurator;
	use Edde\App\Resource\ResourceManagerConfigurator;
	use Edde\App\Router\RouterServiceConfigurator;
	use Edde\Common\File\RootDirectory;
	use Edde\Ext\Cache\ContextCacheManagerConfigurator;
	use Edde\Ext\Container\ClassFactory;
	use Edde\Ext\Container\ContainerFactory;
	use Edde\Ext\Router\RequestQueueConfigurator;
	use Edde\Ext\Template\CompilerConfigurator;
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
	return ContainerFactory::container($factoryList = array_merge(ContainerFactory::getDefaultFactoryList(), [
		IRootDirectory::class    => ContainerFactory::instance(RootDirectory::class, [__DIR__]),
		/**
		 * this application is using specific contexts to separate user experience
		 */
		IContext::class          => AppContext::class,
		/**
		 * when context is changes, one also should (not necessarily) change resource provider to get
		 * ability to search for assets (resources) based on the current context
		 */
		IResourceProvider::class => IContext::class,
	], is_array($local = @include __DIR__ . '/loader.local.php') ? $local : [], [
		new ClassFactory(),
	]), [
		/**
		 * As we have some custom configuration for router service, we have to register proper configurator for it
		 */
		IRouterService::class   => RouterServiceConfigurator::class,
		IRequestQueue::class    => RequestQueueConfigurator::class,
		/**
		 * Because we are using context, we also have to properly setup cache manager (by setting proper namespace from
		 * context)
		 */
		ICacheManager::class    => ContextCacheManagerConfigurator::class,
		/**
		 * We are using some custom resource providers, so we have to register them to resource manager and the current
		 * point how to get resources
		 */
		IResourceManager::class => ResourceManagerConfigurator::class,
		/**
		 * To enable general content exchange, we have to setup converter manager; it basically allows to do arbitrary
		 * data convertions for example json to array, xml file to INode, ... this component is kind of fundamental part
		 * of the framework.
		 */
		IConverterManager::class => ConverterManagerConfigurator::class,
		/**
		 * As other components, Template engine should be configured too; this will register default set of macros
		 */
		ICompiler::class         => CompilerConfigurator::class,
	]);
	// ], __DIR__ . '/temp/container-' . sha1(implode('', array_keys($factoryList)) . new Framework()) . '.cache');

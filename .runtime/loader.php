<?php
	/**
	 * This script is responsible for container creation, thus this is kind of bootstrapper.
	 *
	 * There should not be any kind of "heavy" code, constants and other shits usually used in
	 * this type of file; main purpose is container configuration and creation, it's not necessary
	 * to do any other tasks here.
	 */
	declare(strict_types=1);

	use Edde\Api\Application\IContext;
	use Edde\Api\Cache\ICacheManager;
	use Edde\Api\File\IRootDirectory;
	use Edde\Api\Resource\IResourceProvider;
	use Edde\Api\Router\IRouterService;
	use Edde\App\Application\AppContext;
	use Edde\App\Router\RouterServiceConfigurator;
	use Edde\Common\Container\Factory\ClassFactory;
	use Edde\Common\File\RootDirectory;
	use Edde\Ext\Cache\ContextCacheManagerConfigurator;
	use Edde\Ext\Container\ContainerFactory;
	use Edde\Ext\Router\RestRouter;
	use Edde\Ext\Router\RestRouterConfigurator;
	use Tracy\Debugger;

	/**
	 * All required dependencies here; to prevent "folder up jumps" in path, this file
	 * should see all other required loaders.
	 */
	require_once __DIR__ . '/lib/autoload.php';
	require_once __DIR__ . '/../loader.php';
	require_once __DIR__ . '/src/loader.php';

	/**
	 * Tracy is a bit piece of shit, but quite useful; there is only problem with not so much
	 * transparent configuration through properties (this is the only example of acceptable
	 * scripted thing in this file).
	 */
	Debugger::enable(($isLocal = file_exists($local = __DIR__ . '/loader.local.php')) ? Debugger::DEVELOPMENT : Debugger::PRODUCTION, __DIR__ . '/logs');
	Debugger::$strictMode = true;
	Debugger::$showBar = $isLocal;
	Debugger::$onFatalError[] = function ($e) {
		Debugger::log($e);
	};

	/**
	 * Container factory is the simplest way how to create dependency container; in this particular case container is also
	 * configured to get "default" set of services defined in Edde.
	 *
	 * There is also option to create only container itself without any internal dependencies (not so much recommended except
	 * you are heavy masochist).
	 */
	return ContainerFactory::container($factoryList = array_merge(ContainerFactory::getDefaultFactoryList(), [
		/**
		 * With this piece of shit are problems all the times, but by this application knows, where is it's
		 * (repository)root.
		 *
		 * All other directories should be dependent on this interface.
		 */
		IRootDirectory::class    => ContainerFactory::instance(RootDirectory::class, [__DIR__]),
		/**
		 * This application is using specific contexts to separate user experience
		 */
		IContext::class          => AppContext::class,
		/**
		 * When context is changes, one also should (not necessarily) change resource provider to get
		 * ability to search for assets (resources) based on the current context.
		 */
		IResourceProvider::class => IContext::class,
		/**
		 * This is quite magical, but local loader file should be able to override services defined by default, thus
		 * it must be after application dependency definitions.
		 */
	], is_array($local = @include $local) ? $local : [], [
		/**
		 * This stranger here is last, because it's canHandle method is able to kill a lot of dependencies and
		 * create not so much nice surprises. Thus, it must be last as kind of dependency fallback.
		 */
		new ClassFactory(),
	]), [
		/**
		 * As we have some custom configuration for router service, we have to register proper configurator for it.
		 */
		IRouterService::class => RouterServiceConfigurator::class,
		/**
		 * Because we are using context, we also have to properly setup cache manager (by setting proper namespace from
		 * context).
		 */
		ICacheManager::class  => ContextCacheManagerConfigurator::class,
		RestRouter::class     => RestRouterConfigurator::class,
	]);

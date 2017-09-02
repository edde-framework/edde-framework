<?php
	/**
	 * This script is responsible for container creation, thus this is kind of bootstrapper.
	 *
	 * There should not be any kind of "heavy" code, constants and other shits usually used in
	 * this type of file; main purpose is container configuration and creation, it's not necessary
	 * to do any other tasks here.
	 */
	declare(strict_types=1);

	use Edde\Api\Cache\ICacheManager;
	use Edde\Common\Container\Factory\CascadeFactory;
	use Edde\Common\Container\Factory\ClassFactory;
	use Edde\Ext\Cache\ContextCacheManagerConfigurator;
	use Edde\Ext\Container\ContainerFactory;
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
	return ContainerFactory::containerWithRoot($factoryList = array_merge([/**
	 * This is quite magical, but local loader file should be able to override services defined by default, thus
	 * it must be after application dependency definitions.
	 */
	], is_array($local = @include $local) ? $local : [], [
		/**
		 * This stranger here must be last, because it's canHandle method is able to kill a lot of dependencies and
		 * create not so much nice surprises. Thus, it must be last as kind of dependency fallback.
		 */
		new ClassFactory(),
		/**
		 * This one is one of the most magical: this factory uses IContext::cascade() to search for class; this is quite
		 * epic feature, but also less transparent, useful to seamlessly switch context.
		 */
		new CascadeFactory(),
	]), [
		/**
		 * Because we are using context, we also have to properly setup cache manager (by setting proper namespace from
		 * context).
		 */
		ICacheManager::class => ContextCacheManagerConfigurator::class,
	]);

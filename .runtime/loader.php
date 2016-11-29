<?php
	/**
	 * file responsible for requiring all dependencies
	 */
	declare(strict_types = 1);

	use Edde\Api\Container\IContainer;
	use Edde\Api\File\IRootDirectory;
	use Edde\Api\Router\IRouterService;
	use Edde\Common\Container\Factory\ClassFactory;
	use Edde\Common\File\RootDirectory;
	use Edde\Common\Runtime\Event\ContainerEvent;
	use Edde\Common\Runtime\Event\SetupEvent;
	use Edde\Common\Runtime\Runtime;
	use Edde\Ext\Router\SimpleHttpRouter;
	use Edde\Module\ApplicationModule;
	use Edde\Module\ContainerModule;
	use Edde\Module\HttpModule;
	use Edde\Module\LoggerModule;
	use Edde\Module\WebModule;

	require_once __DIR__ . '/vendor/autoload.php';
	require_once __DIR__ . '/../src/loader.php';
	require_once __DIR__ . '/src/loader.php';

	$factoryList = array_merge([
		IRootDirectory::class => new RootDirectory(__DIR__),
	], is_array($local = @include __DIR__ . '/loader.local.php') ? $local : []);

	$runtime = new Runtime($factoryList);
	$runtime->moduleList([
		new ContainerModule(),
		new ApplicationModule(),
		new WebModule(),
		new HttpModule(),
		new LoggerModule(),
	]);
	$runtime->register(SetupEvent::class, function (SetupEvent $setupEvent) {
		$runtime = $setupEvent->getRuntime();
		$runtime->registerFactoryList([
			SimpleHttpRouter::class,
			new ClassFactory(),
		]);
	});
	$runtime->register(ContainerEvent::class, function (ContainerEvent $containerEvent) {
		$runtime = $containerEvent->getRuntime();
		$runtime->deffered(IRouterService::class, function (IContainer $container, IRouterService $routerService) {
			$routerService->registerRouter($container->create(SimpleHttpRouter::class, [
				'Edde\Common',
			]));
		});
	});
	return $runtime;

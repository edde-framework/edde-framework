<?php
	declare(strict_types = 1);

	namespace Edde\Module;

	use Edde\Api\Application\IApplication;
	use Edde\Api\Application\IRequest;
	use Edde\Api\Application\IResponseManager;
	use Edde\Api\Router\IRouterService;
	use Edde\Common\Application\Application;
	use Edde\Common\Application\ResponseManager;
	use Edde\Common\Event\Handler\SelfHandler;
	use Edde\Common\Router\RouterService;
	use Edde\Common\Runtime\Event\SetupEvent;

	class ApplicationModule extends SelfHandler {
		public function setupApplicationModule(SetupEvent $setupEvent) {
			$runtime = $setupEvent->getRuntime();
			$runtime->registerFactoryList([
				IApplication::class => Application::class,
				IRouterService::class => RouterService::class,
				IRequest::class => function (IRouterService $routerService) {
					return $routerService->createRequest();
				},
				IResponseManager::class => ResponseManager::class,
			]);
		}
	}

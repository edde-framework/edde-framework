<?php
	declare(strict_types=1);

	namespace Edde\Common\Router;

	use Edde\Api\Log\LazyLogServiceTrait;
	use Edde\Api\Router\Exception\BadRequestException;
	use Edde\Api\Router\IRequest;
	use Edde\Api\Router\IRouter;
	use Edde\Api\Router\IRouterService;

	class RouterService extends AbstractRouter implements IRouterService {
		use LazyLogServiceTrait;
		/**
		 * @var IRouter[]
		 */
		protected $routerList = [];
		/**
		 * @var IRouter
		 */
		protected $defaultRouter;
		/**
		 * @var IRouter
		 */
		protected $errorRouter;
		/**
		 * @var IRequest
		 */
		protected $request;

		/**
		 * @inheritdoc
		 */
		public function registerRouter(IRouter $router): IRouterService {
			$this->routerList[] = $router;
			return $this;
		}

		/**
		 * @inheritdoc
		 */
		public function registerDefaultRouter(IRouter $router): IRouterService {
			$this->defaultRouter = $router;
			return $this;
		}

		/**
		 * @inheritdoc
		 */
		public function registerErrorRouter(IRouter $router): IRouterService {
			$this->errorRouter = $router;
			return $this;
		}

		/**
		 * @inheritdoc
		 */
		public function canHandle(): bool {
			return true;
		}

		/**
		 * @inheritdoc
		 */
		public function createRequest(): IRequest {
			if ($this->request) {
				return $this->request;
			}
			$request = null;
			try {
				foreach ($this->routerList as $router) {
					$router->setup();
					if ($router->canHandle()) {
						$request = $router->createRequest();
						break;
					}
				}
			} catch (\Throwable $exception) {
				$this->logService->exception($exception);
				if ($this->errorRouter) {
					$this->errorRouter->setup();
					$this->errorRouter->canHandle() && ($request = $this->errorRouter->createRequest());
				}
			}
			if ($request === null && $this->defaultRouter) {
				if ($this->defaultRouter) {
					$this->defaultRouter->setup();
					$this->defaultRouter->canHandle() && ($request = $this->defaultRouter->createRequest());
				}
			}
			if ($request === null) {
				throw new BadRequestException('Cannot handle current request.');
			}
			return $this->request = $request;
		}
	}

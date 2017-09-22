<?php
	declare(strict_types=1);

	namespace Edde\Common\Router;

	use Edde\Api\Log\Inject\LogService;
	use Edde\Api\Router\Exception\BadRequestException;
	use Edde\Api\Router\IRequest;
	use Edde\Api\Router\IRouter;
	use Edde\Api\Router\IRouterService;

	class RouterService extends AbstractRouter implements IRouterService {
		use LogService;
		/**
		 * @var IRouter[]
		 */
		protected $routerList = [];
		/**
		 * @var IRouter
		 */
		protected $errorRouter;
		/**
		 * @var IRouter
		 */
		protected $router;
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
		public function registerErrorRouter(IRouter $router): IRouterService {
			$this->errorRouter = $router;
			return $this;
		}

		/**
		 * @inheritdoc
		 */
		public function canHandle(): bool {
			try {
				foreach ($this->routerList as $router) {
					if ($router->setup() && $router->canHandle()) {
						$this->router = $router;
						return true;
					}
				}
			} catch (\Exception $exception) {
				$this->logService->exception($exception, [
					'edde',
					'router-service',
				]);
			}
			return false;
		}

		/**
		 * @inheritdoc
		 */
		public function createRequest(): IRequest {
			if ($this->request) {
				return $this->request;
			} else if ($this->router) {
				return $this->request = $this->router->createRequest();
			}
			$request = null;
			try {
				foreach ($this->routerList as $router) {
					if ($router->setup() && $router->canHandle()) {
						$request = $router->createRequest();
						break;
					}
				}
			} catch (\Throwable $exception) {
				$this->logService->exception($exception);
				if ($this->errorRouter) {
					$this->errorRouter->setup() && $this->errorRouter->canHandle() && ($request = $this->errorRouter->createRequest());
				}
			}
			if ($request === null) {
				throw new BadRequestException('Cannot handle current request.');
			}
			return $this->request = $request;
		}
	}

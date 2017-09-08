<?php
	declare(strict_types=1);

	namespace Edde\Common\Router;

	use Edde\Api\Log\LazyLogServiceTrait;
	use Edde\Api\Router\Exception\BadRequestException;
	use Edde\Api\Router\IRequest;
	use Edde\Api\Router\IRouter;
	use Edde\Api\Router\IRouterProxy;
	use Edde\Api\Router\IRouterService;
	use Edde\Common\Object\Object;

	class RouterService extends Object implements IRouterService {
		use LazyLogServiceTrait;
		/**
		 * @var IRouter[]
		 */
		protected $routerList = [];
		/**
		 * @var IRouterProxy[]
		 */
		protected $routerProxyList = [];
		/**
		 * @var IRouterProxy
		 */
		protected $defaultRouterProxy;
		/**
		 * @var IRouterProxy
		 */
		protected $errorRouterProxy;
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
		public function registerRouterProxy(IRouterProxy $routerProxy): IRouterService {
			$this->routerProxyList[] = $routerProxy;
			return $this;
		}

		/**
		 * @inheritdoc
		 */
		public function registerDefaultRouterProxy(IRouterProxy $routerProxy): IRouterService {
			$this->defaultRouterProxy = $routerProxy;
			return $this;
		}

		/**
		 * @inheritdoc
		 */
		public function registerErrorRouterProxy(IRouterProxy $routerProxy): IRouterService {
			$this->errorRouterProxy = $routerProxy;
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
				if ($request === null) {
					foreach ($this->routerProxyList as $routerProxy) {
						$routerProxy->setup();
						$this->registerRouter($router = $routerProxy->proxy());
						if ($router->canHandle()) {
							$request = $router->createRequest();
							break;
						}
					}
				}
			} catch (\Throwable $exception) {
				$this->logService->exception($exception);
				/**
				 * error proxy must be set, create router and see if it can handle current request (it should)
				 */
				if ($this->errorRouterProxy && ($errorRouter = $this->errorRouterProxy->proxy()) && $errorRouter->canHandle()) {
					$request = $errorRouter->createRequest();
				}
			}
			if ($request === null && $this->defaultRouterProxy && ($defaultRouter = $this->defaultRouterProxy->proxy()) && $defaultRouter->canHandle()) {
				$request = $defaultRouter->createRequest();
			}
			if ($request === null) {
				throw new BadRequestException('Cannot handle current request.');
			}
			return $this->request = $request;
		}
	}

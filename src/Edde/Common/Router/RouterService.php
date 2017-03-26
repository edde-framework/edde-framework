<?php
	declare(strict_types=1);

	namespace Edde\Common\Router;

	use Edde\Api\Application\IRequest;
	use Edde\Api\Application\IResponseHandler;
	use Edde\Api\Application\LazyResponseManagerTrait;
	use Edde\Api\Container\LazyContainerTrait;
	use Edde\Api\Router\IRouter;
	use Edde\Api\Router\IRouterService;
	use Edde\Api\Router\RouterException;
	use Edde\Common\Config\ConfigurableTrait;
	use Edde\Common\Object;

	/**
	 * Default implementation of a router service.
	 */
	class RouterService extends Object implements IRouterService {
		use LazyContainerTrait;
		use LazyResponseManagerTrait;
		use ConfigurableTrait;
		/**
		 * @var string[]
		 */
		protected $routerList = [];
		/**
		 * @var IRequest
		 */
		protected $defaultRequest;
		/**
		 * @var IResponseHandler
		 */
		protected $defaultResponseHandler;
		/**
		 * @var IRequest
		 */
		protected $request;

		/**
		 * @inheritdoc
		 */
		public function registerRouter(string $router, array $parameterList = []): IRouterService {
			$this->routerList[$router] = [
				$router,
				$parameterList,
			];
			return $this;
		}

		/**
		 * @inheritdoc
		 */
		public function setDefaultRequest(IRequest $request, IResponseHandler $responseHandler = null): IRouterService {
			$this->defaultRequest = $request;
			$this->defaultResponseHandler = $responseHandler;
			return $this;
		}

		/**
		 * @inheritdoc
		 * @throws RouterException
		 */
		public function createRequest(): IRequest {
			if ($this->request) {
				return $this->request;
			}
			foreach ($this->routerList as $router) {
				list($class, $parameterList) = $router;
				/** @var $router IRouter */
				$router = $this->container->create($class, $parameterList, __METHOD__);
				$router->setup();
				if (($this->request = $router->createRequest()) !== null) {
					return $this->request;
				}
			}
			if ($this->defaultRequest) {
				$this->defaultResponseHandler ? $this->responseManager->setResponseHandler($this->defaultResponseHandler) : null;
				return $this->defaultRequest;
			}
			throw new BadRequestException('Cannot handle current application request.' . (empty($this->routerList) ? ' There are no registered routers.' : ''));
		}
	}

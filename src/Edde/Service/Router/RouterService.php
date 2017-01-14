<?php
	declare(strict_types=1);

	namespace Edde\Service\Router;

	use Edde\Api\Application\IRequest;
	use Edde\Api\Router\IRouterService;
	use Edde\Api\Router\RouterException;
	use Edde\Common\Container\ConfigurableTrait;
	use Edde\Common\Router\RouterList;

	/**
	 * Default implementation of a router service.
	 */
	class RouterService extends RouterList implements IRouterService {
		use ConfigurableTrait;
		/**
		 * @no-cache
		 * @var IRequest
		 */
		protected $request;

		/** @noinspection PhpMissingParentCallCommonInspection */
		/**
		 * @inheritdoc
		 * @throws RouterException
		 */
		public function createRequest(): IRequest {
			if ($this->request) {
				return $this->request;
			}
			foreach ($this->routerList as $router) {
				if (($this->request = $router->createRequest()) !== null) {
					return $this->request;
				}
			}
			throw new BadRequestException('Cannot handle current application request.' . (empty($this->routerList) ? ' There are no registered routers.' : ''));
		}
	}

<?php
	declare(strict_types = 1);

	namespace Edde\Common\Router;

	use Edde\Api\Application\IRequest;
	use Edde\Api\Router\IRouterService;
	use Edde\Api\Router\RouterException;
	use Edde\Common\Container\ConfigurableTrait;

	/**
	 * Default implementation of a router service.
	 */
	class RouterService extends RouterList implements IRouterService {
		use ConfigurableTrait;
		/**
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
			$this->config();
			$e = null;
			foreach ($this->routerList as $router) {
				try {
					$e = null;
					if (($request = $router->createRequest()) !== null) {
						return $this->request = $request;
					}
				} catch (\Exception $e) {
				}
			}
			throw new BadRequestException('Cannot handle current application request.' . (empty($this->routerList) ? ' There are no registered routers.' : ''), 0, $e);
		}
	}

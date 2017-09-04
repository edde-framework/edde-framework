<?php
	declare(strict_types=1);

	namespace Edde\Common\Router;

	use Edde\Api\Router\IRequest;
	use Edde\Api\Router\IRouterService;
	use Edde\Common\Object\Object;

	class RouterService extends Object implements IRouterService {
		/**
		 * @var IRequest
		 */
		protected $request;

		public function createRequest(): IRequest {
			if ($this->request) {
				return $this->request;
			}
			throw new \Exception('not implemented yet: create request');
			return $this->request = $request;
		}
	}

<?php
	declare(strict_types = 1);

	namespace Edde\Common\Router;

	use Edde\Api\Application\IRequest;
	use Edde\Api\Router\IRouterService;
	use Edde\Api\Router\RouterException;

	class RouterService extends RouterList implements IRouterService {
		/**
		 * @var IRequest
		 */
		protected $request;

		/**
		 * @inheritdoc
		 * @throws RouterException
		 */
		public function createRequest() {
			$this->use();
			if ($this->request === null && ($this->request = parent::createRequest()) === null) {
				throw new BadRequestException('Cannot handle current application request.');
			}
			return $this->request;
		}
	}

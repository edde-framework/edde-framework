<?php
	declare(strict_types = 1);

	namespace Edde\Ext\Router;

	use Edde\Api\Crate\ICrateFactory;
	use Edde\Api\Http\IHttpRequest;
	use Edde\Api\Rest\IService;
	use Edde\Common\Container\LazyInjectTrait;
	use Edde\Common\Router\AbstractRouter;

	class RestRouter extends AbstractRouter {
		use LazyInjectTrait;
		/**
		 * @var IHttpRequest
		 */
		protected $httpRequest;
		/**
		 * @var ICrateFactory
		 */
		protected $crateFactory;
		/**
		 * @var IService[]
		 */
		protected $serviceList = [];

		public function lazyHttpRequest(IHttpRequest $httpRequest) {
			$this->httpRequest = $httpRequest;
		}

		public function registerServiceList(array $serviceList) {
			foreach ($serviceList as $service) {
				$this->registerService($service);
			}
			return $this;
		}

		public function registerService(IService $service) {
			$this->serviceList[] = $service;
			return $this;
		}

		public function createRequest() {
			throw new \Exception('not implemented yet: not updated to use Request');
			$this->use();
			$url = $this->httpRequest->getUrl();
			foreach ($this->serviceList as $service) {
				if ($service->match($url)) {
//					return new Request(get_class($service), $this->httpRequest->getMethod(), $url->getQuery());
				}
			}
			return null;
		}

		protected function prepare() {
		}
	}

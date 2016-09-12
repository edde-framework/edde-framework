<?php
	declare(strict_types = 1);

	namespace Edde\Ext\Router;

	use Edde\Api\Application\IResponseManager;
	use Edde\Api\Crate\ICrateFactory;
	use Edde\Api\Http\IHttpRequest;
	use Edde\Api\Http\IHttpResponse;
	use Edde\Api\Rest\IService;
	use Edde\Common\Application\Request;
	use Edde\Common\Container\LazyInjectTrait;
	use Edde\Common\Router\AbstractRouter;

	class RestRouter extends AbstractRouter {
		use LazyInjectTrait;
		/**
		 * @var IHttpRequest
		 */
		protected $httpRequest;
		/**
		 * @var IHttpResponse
		 */
		protected $httpResponse;
		/**
		 * @var IResponseManager
		 */
		protected $responseManager;
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

		public function lazyHttpResponse(IHttpResponse $httpResponse) {
			$this->httpResponse = $httpResponse;
		}

		public function lazyResponseManager(IResponseManager $responseManager) {
			$this->responseManager = $responseManager;
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
			$this->use();
			$url = $this->httpRequest->getUrl();
			$headerList = $this->httpRequest->getHeaderList();
			foreach ($this->serviceList as $service) {
				if ($service->match($url)) {
					$this->responseManager->setMime('http+' . ($mime = $headerList->getContentType($headerList->getAccept())));
					$this->httpResponse->contentType($mime);
					return new Request('http+' . $headerList->getContentType(), get_class($service), $this->httpRequest->getMethod(), $url->getQuery());
				}
			}
			return null;
		}

		protected function prepare() {
		}
	}

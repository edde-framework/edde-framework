<?php
	declare(strict_types = 1);

	namespace Edde\Ext\Router;

	use Edde\Api\Application\IResponseManager;
	use Edde\Api\Crate\ICrateFactory;
	use Edde\Api\Http\IHeaderList;
	use Edde\Api\Http\IHttpRequest;
	use Edde\Api\Http\IHttpResponse;
	use Edde\Api\Http\IRequestUrl;
	use Edde\Api\Rest\IService;
	use Edde\Common\Application\Request;
	use Edde\Common\Router\AbstractRouter;

	class RestRouter extends AbstractRouter {
		/**
		 * @var IRequestUrl
		 */
		protected $requestUrl;
		/**
		 * @var IHeaderList
		 */
		protected $headerList;
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

		public function lazyRequestUrl(IRequestUrl $requestUrl) {
			$this->requestUrl = $requestUrl;
		}

		public function lazyHeaderList(IHeaderList $headerList) {
			$this->headerList = $headerList;
		}

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
			$mime = $this->headerList->getContentType($this->headerList->getAccept());
			foreach ($this->serviceList as $service) {
				if ($service->match($this->requestUrl)) {
					$this->httpResponse->contentType($mime);
					$this->responseManager->setMime($mime = ('http+' . $mime));
					return new Request($mime, get_class($service), $this->httpRequest->getMethod(), $this->requestUrl->getQuery());
				}
			}
			return null;
		}

		protected function prepare() {
		}
	}

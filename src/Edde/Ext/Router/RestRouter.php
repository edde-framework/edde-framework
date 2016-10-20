<?php
	declare(strict_types = 1);

	namespace Edde\Ext\Router;

	use Edde\Api\Application\LazyResponseManagerTrait;
	use Edde\Api\Crate\ICrateFactory;
	use Edde\Api\Http\LazyHeaderListTrait;
	use Edde\Api\Http\LazyHttpRequestTrait;
	use Edde\Api\Http\LazyHttpResponseTrait;
	use Edde\Api\Http\LazyRequestUrlTrait;
	use Edde\Api\Link\ILinkGenerator;
	use Edde\Api\Rest\IService;
	use Edde\Common\Application\Request;
	use Edde\Common\Router\AbstractRouter;

	class RestRouter extends AbstractRouter implements ILinkGenerator {
		use LazyResponseManagerTrait;
		use LazyRequestUrlTrait;
		use LazyHeaderListTrait;
		use LazyHttpRequestTrait;
		use LazyHttpResponseTrait;
		/**
		 * @var ICrateFactory
		 */
		protected $crateFactory;
		/**
		 * @var IService[]
		 */
		protected $serviceList = [];

		public function registerServiceList(array $serviceList) {
			foreach ($serviceList as $service) {
				$this->registerService($service);
			}
			return $this;
		}

		public function registerService(IService $service) {
			$this->serviceList[get_class($service)] = $service;
			return $this;
		}

		public function createRequest() {
			$this->use();
			$mime = (string)$this->headerList->getContentType($this->headerList->getAccept());
			foreach ($this->serviceList as $service) {
				if ($service->match($this->requestUrl)) {
					$this->httpResponse->setContentType($mime);
					$this->responseManager->setMime($mime = ('http+' . $mime));
					return new Request($mime, get_class($service), $this->httpRequest->getMethod(), $this->requestUrl->getQuery());
				}
			}
			return null;
		}

		public function link($generate, ...$parameterList) {
			$this->use();
			if (is_string($generate) === false || isset($this->serviceList[$generate]) === false) {
				return null;
			}
			return $this->serviceList[$generate]->link($generate, ...$parameterList);
		}
	}

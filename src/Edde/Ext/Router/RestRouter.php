<?php
	declare(strict_types=1);

	namespace Edde\Ext\Router;

	use Edde\Api\Application\LazyResponseManagerTrait;
	use Edde\Api\Config\IConfigurable;
	use Edde\Api\Container\LazyContainerTrait;
	use Edde\Api\Http\LazyHttpRequestTrait;
	use Edde\Api\Http\LazyHttpResponseTrait;
	use Edde\Api\Link\ILinkGenerator;
	use Edde\Api\Rest\IService;
	use Edde\Api\Runtime\LazyRuntimeTrait;
	use Edde\Common\Application\HttpResponseHandler;
	use Edde\Common\Application\Request;
	use Edde\Common\Router\AbstractRouter;

	class RestRouter extends AbstractRouter implements IConfigurable, ILinkGenerator {
		use LazyContainerTrait;
		use LazyHttpRequestTrait;
		use LazyHttpResponseTrait;
		use LazyRuntimeTrait;
		use LazyResponseManagerTrait;
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

		/**
		 * @inheritdoc
		 */
		public function createRequest() {
			if ($this->runtime->isConsoleMode() || empty($this->serviceList)) {
				return null;
			}
			$requestUrl = $this->httpRequest->getRequestUrl();
			foreach ($this->serviceList as $service) {
				if ($service->match($requestUrl)) {
					$this->responseManager->setResponseHandler($this->container->create(HttpResponseHandler::class));
					return $this->container->create(Request::class, [
						get_class($service),
						$this->httpRequest->getMethod(),
						$requestUrl->getParameterList(),
						$this->httpRequest->getContent(),
					], __METHOD__);
				}
			}
			return null;
		}

		/**
		 * @inheritdoc
		 */
		public function link($generate, ...$parameterList) {
			if (is_string($generate) === false || isset($this->serviceList[$generate]) === false) {
				return null;
			}
			return $this->serviceList[$generate]->link($generate, ...$parameterList);
		}
	}

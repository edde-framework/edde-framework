<?php
	declare(strict_types=1);

	namespace Edde\Ext\Router;

	use Edde\Api\Config\IConfigurable;
	use Edde\Api\Http\LazyHttpRequestTrait;
	use Edde\Api\Http\LazyHttpResponseTrait;
	use Edde\Api\Http\LazyRequestUrlTrait;
	use Edde\Api\Link\ILinkGenerator;
	use Edde\Api\Rest\IService;
	use Edde\Api\Runtime\LazyRuntimeTrait;
	use Edde\Common\Application\Request;
	use Edde\Common\Router\AbstractRouter;

	class RestRouter extends AbstractRouter implements IConfigurable, ILinkGenerator {
		use LazyRequestUrlTrait;
		use LazyHttpRequestTrait;
		use LazyHttpResponseTrait;
		use LazyRuntimeTrait;
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
			if ($this->runtime->isConsoleMode() || empty($this->serviceList)) {
				return null;
			}
			foreach ($this->serviceList as $service) {
				if ($service->match($this->requestUrl)) {
					return (new Request())->registerActionHandler(get_class($service), $this->httpRequest->getMethod(), $this->requestUrl->getQuery());
				}
			}
			return null;
		}

		public function link($generate, ...$parameterList) {
			if (is_string($generate) === false || isset($this->serviceList[$generate]) === false) {
				return null;
			}
			return $this->serviceList[$generate]->link($generate, ...$parameterList);
		}
	}

<?php
	declare(strict_types=1);

	namespace Edde\Ext\Router;

	use Edde\Api\Application\LazyResponseManagerTrait;
	use Edde\Api\Container\LazyContainerTrait;
	use Edde\Api\Http\LazyHttpRequestTrait;
	use Edde\Api\Runtime\LazyRuntimeTrait;
	use Edde\Common\Application\HttpResponseHandler;
	use Edde\Common\Application\Request;
	use Edde\Common\Router\AbstractRouter;
	use Edde\Common\Strings\StringUtils;

	/**
	 * Simple http router implementation without any additional magic.
	 */
	class HttpRouter extends AbstractRouter {
		use LazyHttpRequestTrait;
		use LazyContainerTrait;
		use LazyResponseManagerTrait;
		use LazyRuntimeTrait;

		/**
		 * @inheritdoc
		 */
		public function createRequest() {
			if ($this->runtime->isConsoleMode()) {
				return null;
			}
			$requestUrl = $this->httpRequest->getRequestUrl();
			$parameterList = $requestUrl->getQuery();
			if (isset($parameterList['action']) === false && isset($parameterList['handle']) === false) {
				return null;
			}
			$request = $this->container->create(Request::class);
			if (isset($parameterList['handle'])) {
				list($control, $handle) = explode('.', $parameterList['handle']);
				unset($parameterList['handle']);
				$request->registerHandleHandler($control, 'handle' . StringUtils::toCamelCase($handle), $parameterList);
			}
			if (isset($parameterList['action'])) {
				list($control, $action) = explode('.', $parameterList['action']);
				unset($parameterList['action']);
				$request->registerActionHandler($control, 'action' . StringUtils::toCamelCase($action), $parameterList);
			}
			$this->responseManager->registerResponseHandler($this->container->create(HttpResponseHandler::class));
			return $request;
		}
	}

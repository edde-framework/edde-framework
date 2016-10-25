<?php
	declare(strict_types = 1);

	namespace Edde\Ext\Router;

	use Edde\Api\Application\IRequest;
	use Edde\Api\Application\LazyResponseManagerTrait;
	use Edde\Api\Crate\LazyCrateFactoryTrait;
	use Edde\Api\Http\LazyBodyTrait;
	use Edde\Api\Http\LazyHeaderListTrait;
	use Edde\Api\Http\LazyHttpRequestTrait;
	use Edde\Api\Http\LazyHttpResponseTrait;
	use Edde\Api\Http\LazyPostListTrait;
	use Edde\Api\Http\LazyRequestUrlTrait;
	use Edde\Api\Runtime\LazyRuntimeTrait;
	use Edde\Common\Application\Request;
	use Edde\Common\Router\AbstractRouter;
	use Edde\Common\Strings\StringUtils;

	/**
	 * Simple http router implementation without any additional magic.
	 */
	class HttpRouter extends AbstractRouter {
		use LazyResponseManagerTrait;
		use LazyBodyTrait;
		use LazyRequestUrlTrait;
		use LazyHeaderListTrait;
		use LazyPostListTrait;
		use LazyHttpRequestTrait;
		use LazyHttpResponseTrait;
		use LazyRuntimeTrait;
		use LazyCrateFactoryTrait;

		/**
		 * @inheritdoc
		 */
		public function createRequest() {
			$this->use();
			if ($this->runtime->isConsoleMode()) {
				return null;
			}
			$parameterList = $this->requestUrl->getQuery();
			if (isset($parameterList['context'], $parameterList['handle'])) {
				return $this->handleContextRequest();
			} else if (isset($parameterList['handle'])) {
				return $this->handleHandleRequest();
			}
			return null;
		}

		/**
		 * when context, context and handle are executed
		 *
		 * @return IRequest|null
		 */
		protected function handleContextRequest() {
			list($context, $contexHandle) = explode('.', $this->requestUrl->getParameter('context'));
			list($handle, $handleHandle) = explode('.', $this->requestUrl->getParameter('handle'));
			$contextMethod = 'context' . ($contexHandle = StringUtils::camelize($contexHandle));
			$handleMethod = 'handle' . ($handleHandle = StringUtils::camelize($handleHandle));
			$parameterList = $this->requestUrl->getQuery();
			unset($parameterList['context'], $parameterList['handle']);
			return $this->request($parameterList)
				->registerHandler($context, $contextMethod)
				->registerHandler($handle, $handleMethod);
		}

		/**
		 * prepare request
		 *
		 * @param array $parameterList
		 *
		 * @return IRequest
		 */
		protected function request(array $parameterList) {
			$this->httpResponse->setContentType($mime = $this->headerList->getContentType()
				->getMime($this->headerList->getAccept()));
			$this->responseManager->setMime($mime = ('http+' . $mime));
			if ($this->httpRequest->isMethod('GET') === false && ($source = ($this->postList->isEmpty() ? $this->body->convert('array') : $this->postList->array())) !== null) {
				$parameterList = array_merge($parameterList, $this->crateFactory->build($source));
			}
			return new Request($mime, $parameterList);
		}

		/**
		 * handle is the only executed method
		 *
		 * @return IRequest|null
		 */
		protected function handleHandleRequest() {
			list($class, $handle) = explode('.', $this->requestUrl->getParameter('handle'));
			$method = 'handle' . ($handle = StringUtils::camelize($handle));
			$parameterList = $this->requestUrl->getQuery();
			unset($parameterList['handle']);
			return $this->request($parameterList)
				->registerHandler($class, $method);
		}
	}

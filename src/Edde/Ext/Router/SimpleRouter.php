<?php
	declare(strict_types = 1);

	namespace Edde\Ext\Router;

	use Edde\Api\Application\IResponseManager;
	use Edde\Api\Crate\ICrateFactory;
	use Edde\Api\Http\IHeaderList;
	use Edde\Api\Http\IHttpRequest;
	use Edde\Api\Http\IHttpResponse;
	use Edde\Api\Http\IPostList;
	use Edde\Api\Http\IRequestUrl;
	use Edde\Api\Runtime\IRuntime;
	use Edde\Common\Application\Request;
	use Edde\Common\Router\AbstractRouter;
	use Edde\Common\Strings\StringUtils;

	/**
	 * Simple router does no magic around uri: incoming request is directly remapped to a class.
	 *
	 * Only difference is for GET/POST -> action/handle method mapping.
	 */
	class SimpleRouter extends AbstractRouter {
		/**
		 * @var IRuntime
		 */
		protected $runtime;
		/**
		 * @var IRequestUrl
		 */
		protected $requestUrl;
		/**
		 * @var IHeaderList
		 */
		protected $headerList;
		/**
		 * @var IPostList
		 */
		protected $postList;
		/**
		 * @var ICrateFactory
		 */
		protected $crateFactory;
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
		 * @param IRuntime $runtime
		 */
		public function lazyRuntime(IRuntime $runtime) {
			$this->runtime = $runtime;
		}

		public function lazyRequestUrl(IRequestUrl $requestUrl) {
			$this->requestUrl = $requestUrl;
		}

		public function lazyHeaderList(IHeaderList $headerList) {
			$this->headerList = $headerList;
		}

		public function lazyPostList(IPostList $postList) {
			$this->postList = $postList;
		}

		public function lazyCrateFactory(ICrateFactory $crateFactory) {
			$this->crateFactory = $crateFactory;
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

		public function createRequest() {
			$this->use();
			if ($this->runtime->isConsoleMode()) {
				return null;
			}
			$class = $this->requestUrl->getParameter('control', false);
			$action = $this->requestUrl->getParameter('action', false);
			if ($action === false) {
				$pathList = $this->requestUrl->getPathList();
				$action = StringUtils::camelize(array_pop($pathList));
				foreach ($pathList as &$path) {
					$path = StringUtils::camelize($path);
				}
				unset($path);
				if (class_exists($class = implode('\\', $pathList)) === false) {
					return null;
				}
			}
			$action = StringUtils::camelize($action);
			$method = 'action' . $action;
			$crateList = [];
			if ($this->httpRequest->isMethod('POST')) {
				$method = 'handle' . $action;
				if ($this->postList->isEmpty() === false) {
					$crateList = $this->crateFactory->build($this->postList->array());
				}
			}
			$parameterList = $this->requestUrl->getQuery();
			unset($parameterList['control'], $parameterList['action']);
			$this->httpResponse->contentType($mime = $this->headerList->getContentType($this->headerList->getAccept()));
			$this->responseManager->setMime($mime = ('http+' . $mime));
			return new Request($mime, $class, $method, array_merge($parameterList, $crateList));
		}

		protected function prepare() {
		}
	}

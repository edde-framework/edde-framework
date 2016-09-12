<?php
	declare(strict_types = 1);

	namespace Edde\Ext\Router;

	use Edde\Api\Application\IResponseManager;
	use Edde\Api\Crate\ICrateFactory;
	use Edde\Api\Http\IHttpRequest;
	use Edde\Api\Http\IHttpResponse;
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
			$url = $this->httpRequest->getUrl();
			$headerList = $this->httpRequest->getHeaderList();
			$class = $url->getParameter('control', false);
			$action = $url->getParameter('action', false);
			if ($action === false) {
				$pathList = $url->getPathList();
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
				$postList = $this->httpRequest->getPostList();
				if ($postList->isEmpty() === false) {
					$crateList = $this->crateFactory->build($postList->array());
				}
			}
			$parameterList = $url->getQuery();
			unset($parameterList['control'], $parameterList['action']);
			$this->responseManager->setMime('http+' . ($mime = $headerList->getContentType($headerList->getAccept())));
			$this->httpResponse->contentType($mime);
			return new Request($mime, $class, $method, array_merge($parameterList, $crateList));
		}

		protected function prepare() {
		}
	}

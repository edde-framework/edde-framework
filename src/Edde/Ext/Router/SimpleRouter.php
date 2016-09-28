<?php
	declare(strict_types = 1);

	namespace Edde\Ext\Router;

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
	 * Simple router does no magic around uri: incoming request is directly remapped to a class.
	 *
	 * Only difference is for GET/POST -> action/handle method mapping.
	 */
	class SimpleRouter extends AbstractRouter {
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
				if (($source = ($this->postList->isEmpty() ? $this->body->convert('array') : $this->postList->array())) !== null) {
					$crateList = $this->crateFactory->build($source);
				}
			}
			$parameterList = $this->requestUrl->getQuery();
			unset($parameterList['control'], $parameterList['action']);
			$this->httpResponse->setContentType($mime = $this->headerList->getContentType($this->headerList->getAccept()));
			$this->responseManager->setMime($mime = ('http+' . $mime));
			return new Request($mime, $class, $method, array_merge($parameterList, $crateList));
		}

		/**
		 * @inheritdoc
		 */
		protected function prepare() {
		}
	}

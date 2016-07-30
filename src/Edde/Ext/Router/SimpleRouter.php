<?php
	namespace Edde\Ext\Router;

	use Edde\Api\Crate\ICrateFactory;
	use Edde\Api\Http\IHttpRequest;
	use Edde\Api\Runtime\IRuntime;
	use Edde\Common\Container\LazyInjectTrait;
	use Edde\Common\Router\AbstractRouter;
	use Edde\Common\Router\Route;
	use Edde\Common\Strings\StringUtils;

	/**
	 * Simple router does no magic around uri: incoming request is directly remapped to a class.
	 *
	 * Only difference is for GET/POST -> action/handle method mapping.
	 */
	class SimpleRouter extends AbstractRouter {
		use LazyInjectTrait;
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
		 * @param IRuntime $runtime
		 */
		public function __construct(IRuntime $runtime) {
			$this->runtime = $runtime;
		}

		final public function lazyCrateFactory(ICrateFactory $crateFactory) {
			$this->crateFactory = $crateFactory;
		}

		final public function lazyHttpRequest(IHttpRequest $httpRequest) {
			$this->httpRequest = $httpRequest;
		}

		public function route() {
			$this->usse();
			if ($this->runtime->isConsoleMode()) {
				return null;
			}
			$url = $this->httpRequest->getUrl();
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
			$method = 'action' . $action;
			$crateList = [];
			if ($this->httpRequest->isMethod('POST')) {
				$method = 'handle' . $action;
				$crateList = $this->crateFactory->build($this->httpRequest->getPostList()
					->getList());
			}
			$parameterList = $url->getQuery();
			unset($parameterList['control'], $parameterList['action']);
			return new Route($class, $method, $parameterList, $crateList);
		}

		protected function prepare() {
		}
	}

<?php
	namespace Edde\Ext\Router;

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
		 * @var IHttpRequest
		 */
		protected $httpRequest;

		public function __construct(IRuntime $runtime) {
			$this->runtime = $runtime;
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
			$pathList = $url->getPathList();
			$action = StringUtils::camelize(array_pop($pathList));
			foreach ($pathList as &$path) {
				$path = StringUtils::camelize($path);
			}
			unset($path);
			if (class_exists($class = implode('\\', $pathList)) === false) {
				return null;
			}
			$method = 'action' . $action;
			if ($this->httpRequest->isMethod('POST')) {
				$method = 'handle' . $action;
			}
			return new Route($class, $method, $url->getQuery());
		}

		protected function prepare() {
		}
	}

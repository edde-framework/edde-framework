<?php
	namespace Edde\Ext\Router;

	use Edde\Api\Container\IContainer;
	use Edde\Api\Crate\ICrate;
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
		 * @var IContainer
		 */
		protected $container;
		/**
		 * @var IHttpRequest
		 */
		protected $httpRequest;

		/**
		 * @param IRuntime $runtime
		 * @param IContainer $container
		 */
		public function __construct(IRuntime $runtime, IContainer $container) {
			$this->runtime = $runtime;
			$this->container = $container;
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
				foreach ($this->httpRequest->getPostList() as $name => $post) {
					/** @var $crate ICrate */
					$crate = $this->container->create($name);
					$crate->push($post);
					$crateList[] = $crate;
				}
			}
			$parameterList = $url->getQuery();
			unset($parameterList['control'], $parameterList['action']);
			return new Route($class, $method, $parameterList, $crateList);
		}

		protected function prepare() {
		}
	}

<?php
	declare(strict_types = 1);

	namespace Edde\Ext\Router;

	use Edde\Api\Crate\ICrateFactory;
	use Edde\Api\Http\IHttpRequest;
	use Edde\Common\Container\LazyInjectTrait;
	use Edde\Common\Router\AbstractRouter;
	use Edde\Common\Router\Route;
	use Edde\Common\Strings\StringUtils;

	class RestRouter extends AbstractRouter {
		use LazyInjectTrait;
		/**
		 * @var string
		 */
		protected $namespace;
		/**
		 * @var IHttpRequest
		 */
		protected $httpRequest;
		/**
		 * @var ICrateFactory
		 */
		protected $crateFactory;

		/**
		 * @param string $namespace
		 */
		public function __construct(string $namespace) {
			$this->namespace = $namespace;
		}

		public function lazyHttpRequest(IHttpRequest $httpRequest) {
			$this->httpRequest = $httpRequest;
		}

		public function lazyCrateFactory(ICrateFactory $crateFactory) {
			$this->crateFactory = $crateFactory;
		}

		public function route() {
			$url = $this->httpRequest->getUrl();
			$pathList = $url->getPathList();
			if (count($pathList) < 3 || $pathList[0] !== 'api') {
				return null;
			}
			array_shift($pathList);
			$id = count($pathList) === 3 ? array_pop($pathList) : null;
			$namespace = StringUtils::camelize($pathList[1]);
			$namespace = $this->namespace . '\\' . ($pathList[0] . '\\' . $namespace . '\\' . $namespace . 'Api');
			if (class_exists($namespace) === false) {
				return null;
			}
			$parameterList = $url->getQuery();
			$parameterList['id'] = $id;

			$reflectionClass = new \ReflectionClass($namespace);
			$reflectionMethod = $reflectionClass->getMethod($method = 'handle' . StringUtils::camelize($this->httpRequest->getMethod()));
			$crateList = [];
			if ($reflectionMethod->getNumberOfParameters() > 0 && ($crateName = $reflectionMethod->getParameters()[0]->getClass()) !== null) {
				$crateList[] = $this->crateFactory->crate(json_decode($this->httpRequest->getBody(), true), $crateName->getName());
			}

			return new Route($namespace, $method, $parameterList, $crateList);
		}

		protected function prepare() {
		}
	}
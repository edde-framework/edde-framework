<?php
	declare(strict_types = 1);

	namespace Edde\Common\Application;

	use Edde\Api\Application\ApplicationException;
	use Edde\Api\Container\IContainer;
	use Edde\Api\Router\IRoute;
	use Edde\Common\Callback\Callback;
	use Edde\Common\Container\LazyInjectTrait;

	class Application extends AbstractApplication {
		use LazyInjectTrait;
		/**
		 * @var IRoute
		 */
		protected $route;
		/**
		 * @var IContainer
		 */
		protected $container;

		public function lazyRoute(IRoute $route) {
			$this->route = $route;
		}

		public function lazyContainer(IContainer $container) {
			$this->container = $container;
		}

		public function run() {
			$this->use();
			if (method_exists($control = $this->container->create($this->route->getClass()), $actionMethod = $this->route->getMethod()) === false) {
				/**
				 * ability to process __call methods; the only restriction is execution without parameters
				 */
				return $control->{$actionMethod}();
			}
			$callback = new Callback([
				$control,
				$actionMethod,
			]);
			$parameterList = $this->route->getParameterList();
			$argumentCount = count($argumentList = $this->route->getCrateList());
			foreach ($callback->getParameterList() as $parameter) {
				if (--$argumentCount >= 0) {
					continue;
				}
				if (isset($parameterList[$parameter->getName()]) === false) {
					if ($parameter->isOptional()) {
						continue;
					}
					throw new ApplicationException(sprintf('Missing action parameter [%s::%s(, ...$%s, ...)].', get_class($control), $actionMethod, $parameter->getName()));
				}
				$argumentList[] = $parameterList[$parameter->getName()];
			}
			return $callback->invoke(...$argumentList);
		}

		protected function prepare() {
		}
	}

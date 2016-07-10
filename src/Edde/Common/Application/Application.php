<?php
	namespace Edde\Common\Application;

	use Edde\Api\Application\ApplicationException;
	use Edde\Api\Control\IControlFactory;
	use Edde\Api\Router\IRoute;
	use Edde\Common\Callback\Callback;
	use Edde\Common\Container\LazyInjectTrait;

	class Application extends AbstractApplication {
		use LazyInjectTrait;
		/**
		 * @var IRoute
		 */
		private $route;
		/**
		 * @var IControlFactory
		 */
		private $controlFactory;

		/**
		 * @param IRoute $route
		 * @param IControlFactory $controlFactory
		 */
		public function lazyApplication(IRoute $route, IControlFactory $controlFactory) {
			$this->route = $route;
			$this->controlFactory = $controlFactory;
		}

		public function run() {
			$this->usse();
			if (method_exists($control = $this->controlFactory->create($this->route->getClass()), $actionMethod = $this->route->getMethod()) === false) {
				throw new ApplicationException(sprintf('Missing method [%s::%s()] in the control.', get_class($control), $actionMethod));
			}
			$callback = new Callback([
				$control,
				$actionMethod,
			]);
			$argumentList = [];
			if (($crate = $this->route->getCrate()) !== null) {
				$argumentList[] = $crate;
			}
			$parameterList = $this->route->getParameterList();
			foreach ($callback->getParameterList() as $parameter) {
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

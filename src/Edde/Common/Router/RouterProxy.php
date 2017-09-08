<?php
	declare(strict_types=1);

	namespace Edde\Common\Router;

	use Edde\Api\Container\LazyContainerTrait;
	use Edde\Api\Router\IRouter;
	use Edde\Api\Router\IRouterProxy;
	use Edde\Common\Object\Object;

	class RouterProxy extends Object implements IRouterProxy {
		use LazyContainerTrait;
		/**
		 * @var string
		 */
		protected $router;
		protected $parameterList = [];

		public function __construct(string $router, array $parameterList) {
			$this->router = $router;
			$this->parameterList = $parameterList;
		}

		/**
		 * @inheritdoc
		 */
		public function proxy(): IRouter {
			/** @var $router IRouter */
			$router = $this->container->create($this->router, $this->parameterList, __METHOD__);
			$router->setup();
			return $router;
		}
	}

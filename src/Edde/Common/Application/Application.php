<?php
	declare(strict_types = 1);

	namespace Edde\Common\Application;

	use Edde\Api\Application\ApplicationException;
	use Edde\Api\Application\IErrorControl;
	use Edde\Api\Application\IRequest;
	use Edde\Api\Application\IResponseManager;
	use Edde\Api\Container\IContainer;
	use Edde\Api\Control\IControl;
	use Edde\Api\Resource\IResourceManager;
	use Edde\Common\Application\Event\ErrorEvent;
	use Edde\Common\Application\Event\FinishEvent;
	use Edde\Common\Application\Event\StartEvent;
	use Edde\Common\Container\LazyInjectTrait;

	class Application extends AbstractApplication {
		use LazyInjectTrait;
		/**
		 * @var IRequest
		 */
		protected $request;
		/**
		 * @var IResourceManager
		 */
		protected $responseManager;
		/**
		 * @var IContainer
		 */
		protected $container;
		/**
		 * @var IErrorControl
		 */
		protected $errorControl;

		public function lazyRoute(IRequest $request) {
			$this->request = $request;
		}

		public function lazyResponseManager(IResponseManager $responseManager) {
			$this->responseManager = $responseManager;
		}

		public function lazyContainer(IContainer $container) {
			$this->container = $container;
		}

		public function lazyErrorControl(IErrorControl $errorControl) {
			$this->errorControl = $errorControl;
		}

		public function run() {
			try {
				$this->use();
				$this->event(new StartEvent($this));
				/** @var $control IControl */
				if ((($control = $this->container->create($this->request->getClass())) instanceof IControl) === false) {
					throw new ApplicationException(sprintf('Route class [%s] is not instance of [%s].', $this->request->getClass(), IControl::class));
				}
				$this->event(new FinishEvent($this, $result = $control->handle($this->request->getMethod(), $this->request->getParameterList())));
				return $result;
			} catch (\Exception $e) {
				$this->event(new ErrorEvent($this, $e));
				return $this->errorControl->exception($e);
			}
		}

		protected function prepare() {
		}
	}

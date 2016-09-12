<?php
	declare(strict_types = 1);

	namespace Edde\Common\Application;

	use Edde\Api\Application\ApplicationException;
	use Edde\Api\Application\IErrorControl;
	use Edde\Api\Application\IRequest;
	use Edde\Api\Application\IResponseManager;
	use Edde\Api\Container\IContainer;
	use Edde\Api\Control\IControl;
	use Edde\Api\Converter\IConverterManager;
	use Edde\Common\Application\Event\ErrorEvent;
	use Edde\Common\Application\Event\FinishEvent;
	use Edde\Common\Application\Event\StartEvent;

	class Application extends AbstractApplication {
		/**
		 * @var IRequest
		 */
		protected $request;
		/**
		 * @var IResponseManager
		 */
		protected $responseManager;
		/**
		 * @var IConverterManager
		 */
		protected $converterManager;
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

		public function lazyConverterManager(IConverterManager $converterManager) {
			$this->converterManager = $converterManager;
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
				$this->responseManager->execute();
				return $result;
			} catch (\Exception $e) {
				$this->event(new ErrorEvent($this, $e));
				return $this->errorControl->exception($e);
			}
		}

		protected function prepare() {
		}
	}

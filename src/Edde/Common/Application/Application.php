<?php
	declare(strict_types = 1);

	namespace Edde\Common\Application;

	use Edde\Api\Application\ApplicationException;
	use Edde\Api\Application\IErrorControl;
	use Edde\Api\Application\IRequest;
	use Edde\Api\Application\LazyResponseManagerTrait;
	use Edde\Api\Container\LazyContainerTrait;
	use Edde\Api\Control\IControl;
	use Edde\Api\Converter\LazyConverterManagerTrait;
	use Edde\Common\Application\Event\ErrorEvent;
	use Edde\Common\Application\Event\FinishEvent;
	use Edde\Common\Application\Event\StartEvent;

	/**
	 * Default application implementation.
	 */
	class Application extends AbstractApplication {
		use LazyContainerTrait;
		use LazyConverterManagerTrait;
		use LazyResponseManagerTrait;
		/**
		 * @var IRequest
		 */
		protected $request;
		/**
		 * @var IErrorControl
		 */
		protected $errorControl;

		/**
		 * @param IRequest $request
		 */
		public function lazyRoute(IRequest $request) {
			$this->request = $request;
		}

		/**
		 * @param IErrorControl $errorControl
		 */
		public function lazyErrorControl(IErrorControl $errorControl) {
			$this->errorControl = $errorControl;
		}

		/**
		 * @inheritdoc
		 */
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

		/**
		 * @inheritdoc
		 */
		protected function prepare() {
		}
	}

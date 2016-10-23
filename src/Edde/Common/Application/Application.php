<?php
	declare(strict_types = 1);

	namespace Edde\Common\Application;

	use Edde\Api\Application\ApplicationException;
	use Edde\Api\Application\IErrorControl;
	use Edde\Api\Application\LazyRequestTrait;
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
		use LazyRequestTrait;
		/**
		 * @var IErrorControl
		 */
		protected $errorControl;

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
				$result = null;
				/** @var $controlList IControl[] */
				$controlList = [];
				foreach ($this->request->getHandlerList() as $handler) {
					list($control, $method) = $handler;
					if (isset($controlList[$control]) === false && (($controlList[$control] = $this->container->create($control)) instanceof IControl) === false) {
						throw new ApplicationException(sprintf('Route class [%s] is not instance of [%s].', $control, IControl::class));
					}
					$result = $controlList[$control]->handle($method, $this->request->getParameterList());
				}
				$this->event(new FinishEvent($this, $result));
				$this->responseManager->execute();
				return $result;
			} catch (\Exception $e) {
				$this->event(new ErrorEvent($this, $e));
				return $this->errorControl->exception($e);
			}
		}
	}

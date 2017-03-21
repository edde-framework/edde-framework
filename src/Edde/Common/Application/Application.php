<?php
	declare(strict_types=1);

	namespace Edde\Common\Application;

	use Edde\Api\Application\ApplicationException;
	use Edde\Api\Application\LazyRequestTrait;
	use Edde\Api\Application\LazyResponseManagerTrait;
	use Edde\Api\Container\LazyContainerTrait;
	use Edde\Api\Control\IControl;
	use Edde\Api\Converter\LazyConverterManagerTrait;
	use Edde\Api\Log\LazyLogServiceTrait;

	/**
	 * Default application implementation.
	 */
	class Application extends AbstractApplication {
		use LazyRequestTrait;
		use LazyContainerTrait;
		use LazyConverterManagerTrait;
		use LazyResponseManagerTrait;
		use LazyLogServiceTrait;

		/**
		 * @inheritdoc
		 */
		public function run() {
			try {
				/** @var $control IControl */
				if ((($control = $this->container->create($class = $this->request->getControl(), [], __METHOD__)) instanceof IControl) === false) {
					throw new ApplicationException(sprintf('Route class [%s] is not instance of [%s].', $class, IControl::class));
				}
				$result = $control->handle($this->request->getAction(), $this->request->getParameterList());
				$this->responseManager->execute();
				return $result;
			} catch (\Exception $exception) {
				$this->logService->exception($exception, ['edde']);
				throw $exception;
			}
		}
	}

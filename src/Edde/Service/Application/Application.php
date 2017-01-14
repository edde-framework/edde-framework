<?php
	declare(strict_types=1);

	namespace Edde\Service\Application;

	use Edde\Api\Application\ApplicationException;
	use Edde\Api\Application\LazyRequestTrait;
	use Edde\Api\Application\LazyResponseManagerTrait;
	use Edde\Api\Container\LazyContainerTrait;
	use Edde\Api\Control\IControl;
	use Edde\Api\Converter\LazyConverterManagerTrait;
	use Edde\Api\Log\LazyLogServiceTrait;
	use Edde\Common\Application\AbstractApplication;

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
		 * @throws \Exception
		 */
		public function run() {
			try {
				list($class, $method, $parameterList) = $this->request->getCurrent();
				if ((($control = $this->container->create($class, [], __METHOD__)) instanceof IControl) === false) {
					throw new ApplicationException(sprintf('Route class [%s] is not instance of [%s].', $class, IControl::class));
				}
				$result = $control->handle($method, $parameterList);
				$this->responseManager->execute();
				return $result;
			} catch (\Exception $exception) {
				$this->logService->exception($exception, ['edde']);
				throw $exception;
			}
		}
	}

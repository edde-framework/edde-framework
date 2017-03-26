<?php
	declare(strict_types=1);

	namespace Edde\Common\Application;

	use Edde\Api\Application\ApplicationException;
	use Edde\Api\Application\IRequest;
	use Edde\Api\Application\IResponse;
	use Edde\Api\Container\LazyContainerTrait;
	use Edde\Api\Control\IControl;
	use Edde\Api\Log\LazyLogServiceTrait;

	/**
	 * Default application implementation.
	 */
	class Application extends AbstractApplication {
		use LazyContainerTrait;
		use LazyLogServiceTrait;

		/**
		 * @inheritdoc
		 */
		public function execute(IRequest $request): IResponse {
			try {
				/** @var $control IControl */
				if ((($control = $this->container->create($class = $request->getControl(), [], __METHOD__)) instanceof IControl) === false) {
					throw new ApplicationException(sprintf('Route class [%s] is not instance of [%s].', $class, IControl::class));
				}
				return $control->handle($request->getAction(), $request->getParameterList());
			} catch (\Exception $exception) {
				$this->logService->exception($exception, ['edde']);
				throw $exception;
			}
		}
	}

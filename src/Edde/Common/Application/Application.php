<?php
	declare(strict_types=1);

	namespace Edde\Common\Application;

	use Edde\Api\Application\ApplicationException;
	use Edde\Api\Container\LazyContainerTrait;
	use Edde\Api\Control\IControl;
	use Edde\Api\Log\LazyLogServiceTrait;
	use Edde\Api\Protocol\IElement;

	/**
	 * Default application implementation.
	 */
	class Application extends AbstractApplication {
		use LazyContainerTrait;
		use LazyLogServiceTrait;

		/**
		 * @inheritdoc
		 */
		public function execute(IElement $request): IElement {
			try {
				/** @var $control IControl */
				if ((($control = $this->container->create($class = $request->getControl(), [], __METHOD__)) instanceof IControl) === false) {
					throw new ApplicationException(sprintf('Route class [%s] is not instance of [%s].', $class, IControl::class));
				}
				return $control->request($request);
			} catch (\Exception $exception) {
				$this->logService->exception($exception, ['edde']);
				throw $exception;
			}
		}
	}

<?php
	namespace Edde\Common\Control;

	use Edde\Api\Container\ContainerException;
	use Edde\Api\Container\IContainer;
	use Edde\Api\Control\ControlException;
	use Edde\Api\Control\IControl;
	use Edde\Api\Control\IControlFactory;
	use Edde\Common\AbstractObject;

	class ControlFactory extends AbstractObject implements IControlFactory {
		/**
		 * @var IContainer
		 */
		private $container;

		/**
		 * @param IContainer $container
		 */
		public function __construct(IContainer $container) {
			$this->container = $container;
		}

		public function create($control) {
			try {
				if ((($control = $this->container->create($control)) instanceof IControl) === false) {
					throw new ControlException(sprintf('Given class [%s] is not instance if [%s].', get_class($control), IControl::class));
				}
			} catch (ContainerException $e) {
				throw new ControlException(sprintf('Cannot create requested control [%s].', $control), 0, $e);
			}
			return $control;
		}
	}

<?php
	namespace Edde\Common\Control;

	use Edde\Api\Container\IContainer;
	use Edde\Api\Control\IControl;
	use Edde\Common\Control\Html\ButtonControl;
	use Edde\Common\Control\Html\DivControl;
	use Edde\Common\Control\Html\MetaControl;

	/**
	 * This is helper trait for integration of a factory methods of Edde's control set.
	 */
	trait ControlTrait {
		/**
		 * @var IContainer
		 */
		protected $container;

		final public function injectContainer(IContainer $container) {
			$this->container = $container;
		}

		/**
		 * @return MetaControl
		 */
		public function createMetaControl() {
			return $this->createControl(MetaControl::class);
		}

		/**
		 * @param string $control
		 * @param array ...$parameterList
		 *
		 * @return IControl
		 */
		public function createControl($control, ...$parameterList) {
			return $this->container->create($control, ...$parameterList);
		}

		/**
		 * @return DivControl
		 */
		public function createDivControl() {
			return $this->createControl(DivControl::class);
		}

		/**
		 * @param string $title
		 * @param string $control
		 * @param string $action
		 * @param string|null $hint
		 *
		 * @return ButtonControl
		 */
		public function createButtonControl($title, $control, $action, $hint = null) {
			return $this->createControl(ButtonControl::class, $title, $control, $action, $hint);
		}
	}

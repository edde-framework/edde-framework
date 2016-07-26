<?php
	namespace Edde\Common\Control;

	use Edde\Api\Container\IContainer;
	use Edde\Api\Control\IControl;
	use Edde\Api\Schema\IProperty;
	use Edde\Common\Control\Html\ButtonControl;
	use Edde\Common\Control\Html\DivControl;
	use Edde\Common\Control\Html\MetaControl;
	use Edde\Common\Control\Html\PasswordInputControl;
	use Edde\Common\Control\Html\TextInputControl;

	/**
	 * This is helper trait for integration of a factory methods of Edde's control set.
	 */
	trait ControlTrait {
		/**
		 * @var IContainer
		 */
		protected $container;

		public function injectContainer(IContainer $container) {
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
			$this->addControl($control = $this->container->create($control, ...$parameterList));
			return $control;
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

		/**
		 * @param IProperty $property
		 *
		 * @return TextInputControl
		 */
		public function createTextInputControl(IProperty $property = null) {
			return $this->createControl(TextInputControl::class, $property);
		}

		/**
		 * @param IProperty $property
		 *
		 * @return PasswordInputControl
		 */
		public function createPasswordInputControl(IProperty $property = null) {
			return $this->createControl(PasswordInputControl::class, $property);
		}
	}

<?php
	declare(strict_types = 1);

	namespace Edde\Common\Control;

	use Edde\Api\Container\IContainer;
	use Edde\Api\Control\IControl;
	use Edde\Api\Schema\ISchemaProperty;
	use Edde\Common\Html\ButtonControl;
	use Edde\Common\Html\DivControl;
	use Edde\Common\Html\JavaScriptControl;
	use Edde\Common\Html\MetaControl;
	use Edde\Common\Html\PasswordInputControl;
	use Edde\Common\Html\StyleSheetControl;
	use Edde\Common\Html\TextInputControl;
	use Edde\Common\Html\TitleControl;

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
		 * @return TitleControl
		 */
		public function createTitleControl() {
			return $this->createControl(TitleControl::class);
		}

		/**
		 * @return JavaScriptControl
		 */
		public function createJavaScriptControl() {
			return $this->createControl(JavaScriptControl::class);
		}

		/**
		 * @return StyleSheetControl
		 */
		public function createStyleSheetControl() {
			return $this->createControl(StyleSheetControl::class);
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
		 * @param ISchemaProperty $schemaProperty
		 *
		 * @return TextInputControl
		 */
		public function createTextInputControl(ISchemaProperty $schemaProperty = null) {
			return $this->createControl(TextInputControl::class, $schemaProperty);
		}

		/**
		 * @param ISchemaProperty $schemaProperty
		 *
		 * @return PasswordInputControl
		 */
		public function createPasswordInputControl(ISchemaProperty $schemaProperty = null) {
			return $this->createControl(PasswordInputControl::class, $schemaProperty);
		}
	}

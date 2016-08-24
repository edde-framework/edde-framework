<?php
	use Edde\Common\Html\ButtonControl;
	use Edde\Common\Html\DivControl;
	use Edde\Common\Html\Document\DocumentControl;
	use Edde\Common\Html\SpanControl;
	use Edde\Common\Html\TemplateControl;

	class TestDocument extends DocumentControl {
		public function switchMe() {
			return 'bar';
		}

		public function getItemList() {
			return [
				'first' => 'looped-one',
				'second' => 'another-looop',
			];
		}

		public function getSomeItemList() {
			return [
				'whee' => new SomeItem('whee'),
				'foo' => new SomeItem('foo'),
				'poo' => new SomeItem('poo'),
			];
		}

		public function specialDiv(DivControl $divControl) {
			$divControl->addClass('special-class');
		}

		public function specialSpan(SpanControl $control) {
			$control->addClass('special-span-class');
		}

		public function specialButton(ButtonControl $control) {
			$control->addClass('special-button-class');
		}
	}

	class SomeItem {
		/**
		 * @var string
		 */
		protected $item;

		/**
		 * @param string $item
		 */
		public function __construct($item) {
			$this->item = $item;
		}

		public function getValue() {
			return 'item-value ' . $this->item;
		}

		public function getAnotherValue() {
			return 'another-item-value ' . $this->item;
		}
	}

	class CustomControl extends TemplateControl {
		public function getAttr() {
			return 'foo';
		}

		protected function prepare() {
			parent::prepare();
			$this->setTemplate(__DIR__ . '/template/custom-control.xml');
		}
	}

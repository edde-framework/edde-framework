<?php
	use Edde\Common\Html\DivControl;
	use Edde\Common\Html\Document\DocumentControl;

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

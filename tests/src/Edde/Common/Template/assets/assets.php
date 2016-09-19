<?php
	declare(strict_types = 1);

	use Edde\Common\Html\Document\DocumentControl;
	use Edde\Common\Html\Tag\ButtonControl;
	use Edde\Common\Html\Tag\DivControl;
	use Edde\Common\Html\Tag\SpanControl;

	class TestDocument extends DocumentControl {
		/**
		 * @var DivControl
		 */
		public $snippy;
		/**
		 * @var DivControl
		 */
		public $specialDiv;
		/**
		 * @var DivControl
		 */
		public $message;
		public $specialProperty;

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
			$this->specialDiv = $divControl;
			$divControl->addClass('special-class');
		}

		public function specialSpan(SpanControl $control) {
			$control->addClass('special-span-class');
		}

		public function specialButton(ButtonControl $control) {
			$control->addClass('special-button-class');
		}

		public function loop() {
			for ($i = 0; $i < 3; $i++) {
				yield (function () {
					for ($i = 0; $i < 5; $i++) {
						yield 'item-' . $i;
					}
				})();
			}
		}

		public function render() {
			$this->dirty();
			return parent::render();
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

		public function getAction() {
			return 'some-action-' . $this->item;
		}

		public function getValue() {
			return 'item-value ' . $this->item;
		}

		public function getAnotherValue() {
			return 'another-item-value ' . $this->item;
		}
	}

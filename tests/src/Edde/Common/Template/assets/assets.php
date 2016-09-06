<?php
	declare(strict_types = 1);

	use Edde\Common\Html\Document\DocumentControl;
	use Edde\Common\Html\Tag\ButtonControl;
	use Edde\Common\Html\Tag\DivControl;
	use Edde\Common\Html\Tag\SpanControl;
	use Edde\Common\Html\TemplateControl;

	class TestDocument extends DocumentControl {
		/**
		 * @var DivControl
		 */
		public $snippy;

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

		public function getTemplateFileName() {
			return __DIR__ . '/template/require.xml';
		}

		public function callTheSnippet(DivControl $divControl) {
			$divControl->dirty();
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

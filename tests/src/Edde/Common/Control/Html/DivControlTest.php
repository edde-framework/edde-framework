<?php
	namespace Edde\Common\Control\Html;

	use Edde\Api\Control\Html\HtmlException;
	use phpunit\framework\TestCase;

	class DivControlTest extends TestCase {
		public function testCommon() {
			$divControl = new DivControl();
			ob_start();
			$divControl->render();
			self::assertEquals("<div></div>\n", ob_get_clean());
		}

		public function testDivClassList() {
			$divControl = new DivControl();
			$divControl->addClass('foo');
			$divControl->addClass('bar');
			ob_start();
			$divControl->render();
			self::assertEquals("<div class=\"foo bar\"></div>\n", ob_get_clean());
		}

		public function testDivException() {
			$this->expectException(HtmlException::class);
			$this->expectExceptionMessage('Cannot set tag [poo] for a div control.');
			$divControl = new DivControl();
			$divControl->setTag('poo');
		}

		public function testDivTree() {
			$divControl = new DivControl();
			$divControl->addClass('foo');
			$divControl->addClass('bar');
			$divControl->addControl((new DivControl())->addClass('one'));
			ob_start();
			$divControl->render();
			self::assertEquals('<div class="foo bar">
	<div class="one"></div>
</div>
', ob_get_clean());
		}
	}

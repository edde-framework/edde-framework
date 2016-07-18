<?php
	namespace Edde\Common\Control\Html;

	use Edde\Api\Control\Html\HtmlException;
	use phpunit\framework\TestCase;

	class ImgControlTest extends TestCase {
		public function testCommon() {
			$imgControl = new ImgControl();
			$imgControl->setSrc('/img/some-image.png');
			self::assertEquals("<img src=\"/img/some-image.png\">\n", $imgControl->render());
		}

		public function testImgTreeException() {
			$this->expectException(HtmlException::class);
			$this->expectExceptionMessage(sprintf('Cannot add control to an image control [%s].', ImgControl::class));
			$imgControl = new ImgControl();
			$imgControl->setSrc('/img/some-image.png');
			$imgControl->addControl(new DivControl());
		}
	}

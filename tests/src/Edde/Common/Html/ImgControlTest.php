<?php
	declare(strict_types = 1);

	namespace Edde\Common\Html;

	use Edde\Api\Html\HtmlException;
	use Edde\Common\Html\Tag\DivControl;
	use Edde\Common\Html\Tag\ImgControl;
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

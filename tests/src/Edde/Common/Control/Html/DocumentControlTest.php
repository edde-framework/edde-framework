<?php
	namespace Edde\Common\Control\Html;

	use phpunit\framework\TestCase;

	class DocumentControlTest extends TestCase {
		public function testCommon() {
			$document = new DocumentControl();
			$document->getHead()
				->setTitle('some meaningfull title');
			ob_start();
			$document->render();
			self::assertEquals('<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<title>some meaningfull title</title>
	</head>
	<body></body>
</html>
', ob_get_clean());
		}

		public function testDocumentStyleJavascript() {
			$document = new DocumentControl();
			$head = $document->getHead();
			$head->addJavaScript('/some/javascript/file.js');
			$head->addJavaScript('/another/javascript-file.js');
			$head->addStyleSheet('/style.css');
			$head->addStyleSheet('/stylish-style.css');
			$head->setTitle('some meaningfull title');
			ob_start();
			$document->render();
			self::assertEquals('<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<title>some meaningfull title</title>
		<script src="/some/javascript/file.js"></script>
		<script src="/another/javascript-file.js"></script>
		<link rel="stylesheet" media="all" href="/style.css">
		<link rel="stylesheet" media="all" href="/stylish-style.css">
	</head>
	<body></body>
</html>
', ob_get_clean());
		}
	}

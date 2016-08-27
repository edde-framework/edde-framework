<?php
	declare(strict_types = 1);

	namespace Edde\Common\Html;

	use Edde\Api\Container\IContainer;
	use Edde\Api\Html\IHtmlView;
	use Edde\Api\Web\IJavaScriptCompiler;
	use Edde\Api\Web\IStyleSheetCompiler;
	use Edde\Common\Html\Tag\DivControl;
	use Edde\Common\Web\JavaScriptCompiler;
	use Edde\Common\Web\StyleSheetCompiler;
	use Edde\Ext\Container\ContainerFactory;
	use phpunit\framework\TestCase;

	require_once(__DIR__ . '/assets/assets.php');

	class HtmlViewTest extends TestCase {
		/**
		 * @var IContainer
		 */
		protected $container;
		/**
		 * @var IHtmlView
		 */
		protected $htmlView;

		public function testDirty() {
			$this->htmlView->addControl($this->htmlView->createControl(DivControl::class));
			self::assertEquals('<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<title></title>
		<meta name="viewport" content="width=device-width, initial-scale=1">
	</head>
	<body>
		<div></div>
	</body>
</html>
', $this->htmlView->render());
		}

		public function testSnippet() {
			$this->htmlView->addControl($controlAlpha = $this->htmlView->createControl(DivControl::class));
			$this->htmlView->addControl($controlBeta = $this->htmlView->createControl(DivControl::class));
			$this->htmlView->snippet($controlAlpha, [
				$this->htmlView,
				'myDivSnippet',
			]);
			$this->htmlView->snippet($controlBeta, [
				$this->htmlView,
				'myDummySnippet',
			]);
			self::assertCount(1, $snippets = $this->htmlView->snippets());
			$control = reset($snippets);
			self::assertTrue($control->isDirty());
		}

		protected function setUp() {
			$this->container = ContainerFactory::create([
				IStyleSheetCompiler::class => StyleSheetCompiler::class,
				IJavaScriptCompiler::class => JavaScriptCompiler::class,
			]);
			$this->htmlView = $this->container->create(\MyLittleCuteView::class);
		}
	}

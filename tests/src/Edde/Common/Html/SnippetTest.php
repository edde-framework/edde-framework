<?php
	declare(strict_types = 1);

	namespace Edde\Common\Html;

	use Edde\Api\Container\IContainer;
	use Edde\Api\File\IRootDirectory;
	use Edde\Api\Resource\IResourceManager;
	use Edde\Api\Template\ITemplateDirectory;
	use Edde\Api\Template\ITemplateManager;
	use Edde\Api\Web\IJavaScriptCompiler;
	use Edde\Api\Web\IStyleSheetCompiler;
	use Edde\Common\Container\Factory\ClassFactory;
	use Edde\Common\File\RootDirectory;
	use Edde\Common\File\TempDirectory;
	use Edde\Common\Html\Tag\DivControl;
	use Edde\Common\Resource\ResourceManager;
	use Edde\Common\Template\TemplateManager;
	use Edde\Common\Web\JavaScriptCompiler;
	use Edde\Common\Web\StyleSheetCompiler;
	use Edde\Common\Xml\XmlParser;
	use Edde\Common\Xml\XmlResourceHandler;
	use Edde\Ext\Container\ContainerFactory;
	use phpunit\framework\TestCase;

	require_once(__DIR__ . '/assets/assets.php');

	class SnippetTest extends TestCase {
		/**
		 * @var IContainer
		 */
		protected $container;
		/**
		 * @var \MyLittleCuteView
		 */
		protected $htmlView;

		public function testSnippet() {
			self::assertTrue(true);
			$this->htmlView->snippet($this->container->create(DivControl::class), [
				$this->htmlView,
				'myDivSnippet',
			]);
			$this->htmlView->snippet($this->container->create(DivControl::class), [
				$this->htmlView,
				'myDummySnippet',
			]);
			self::assertCount(1, $this->htmlView->snippets());
		}

		public function testTemplateSnippet() {
			$this->htmlView->template(__DIR__ . '/assets/snippet.xml');
			self::assertEquals('<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<title></title>
		<meta name="viewport" content="width=device-width, initial-scale=1">
	</head>
	<body>
		<div class="whoop!"></div>
		<div class="edde-placeholder" id="foo"></div>
	</body>
</html>
', $this->htmlView->render());
		}

		public function testTemplateSnippetProperty() {
			$this->htmlView->template(__DIR__ . '/assets/snippet-property.xml');
			self::assertInstanceOf(DivControl::class, $this->htmlView->templateSnippet);
			$this->htmlView->templateSnippet->dirty();
			self::assertEquals('<div id="foo" class="this is a snippet!">
	<div class="another control"></div>
	<div>
		<div>deeper and deeper</div>
	</div>
</div>
', $this->htmlView->templateSnippet->render());
			self::assertEquals('<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<title></title>
		<meta name="viewport" content="width=device-width, initial-scale=1">
	</head>
	<body>
		<div class="whoop!"></div>
		<div class="edde-placeholder" id="foo"></div>
	</body>
</html>
', $this->htmlView->render());
		}

		protected function setUp() {
			$this->container = ContainerFactory::create([
				ITemplateManager::class => TemplateManager::class,
				IStyleSheetCompiler::class => StyleSheetCompiler::class,
				IJavaScriptCompiler::class => JavaScriptCompiler::class,
				ITemplateDirectory::class => function () {
					return new TempDirectory(__DIR__ . '/temp');
				},
				IRootDirectory::class => function () {
					return new RootDirectory(__DIR__ . '/temp');
				},
				IResourceManager::class => (new ClassFactory(IResourceManager::class, ResourceManager::class))->onSetup(function (IResourceManager $resourceManager) {
					$resourceManager->registerResourceHandler((new XmlResourceHandler())->lazyXmlParser(new XmlParser()));
				}),
			]);
			$templateManager = $this->container->create(ITemplateManager::class);
			$templateManager->onSetup(function (ITemplateManager $templateManager) {
				$templateManager->registerMacroList(MacroSet::macroList($this->container));
			});
			$this->htmlView = $this->container->create(\MyLittleCuteView::class);
		}
	}

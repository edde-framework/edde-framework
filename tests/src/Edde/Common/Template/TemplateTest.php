<?php
	declare(strict_types = 1);

	namespace Edde\Common\Template;

	use Edde\Api\Container\IContainer;
	use Edde\Api\Crypt\ICryptEngine;
	use Edde\Api\Html\IHtmlControl;
	use Edde\Api\Resource\IResourceManager;
	use Edde\Api\Template\ITemplateDirectory;
	use Edde\Api\Template\ITemplateManager;
	use Edde\Api\Web\IJavaScriptCompiler;
	use Edde\Api\Web\IStyleSheetCompiler;
	use Edde\Common\Crypt\CryptEngine;
	use Edde\Common\Resource\ResourceManager;
	use Edde\Common\Template\Macro\BindIdAttributeMacro;
	use Edde\Common\Template\Macro\ButtonNodeMacro;
	use Edde\Common\Template\Macro\ControlMacro;
	use Edde\Common\Template\Macro\CssNodeMacro;
	use Edde\Common\Template\Macro\DivNodeMacro;
	use Edde\Common\Template\Macro\IncludeNodeMacro;
	use Edde\Common\Template\Macro\JsNodeMacro;
	use Edde\Common\Template\Macro\SwitchNodeMacro;
	use Edde\Common\Web\JavaScriptCompiler;
	use Edde\Common\Web\StyleSheetCompiler;
	use Edde\Common\Xml\XmlParser;
	use Edde\Common\Xml\XmlResourceHandler;
	use Edde\Ext\Container\ContainerFactory;
	use phpunit\framework\TestCase;

	require_once(__DIR__ . '/assets/assets.php');

	class TemplateTest extends TestCase {
		/**
		 * @var IResourceManager
		 */
		protected $resourceManager;
		/**
		 * @var IContainer
		 */
		protected $container;
		/**
		 * @var ITemplateManager
		 */
		protected $templateManager;
		/**
		 * @var IHtmlControl
		 */
		protected $control;

		public function testSwitchTemplate() {
			$template = $this->templateManager->template(__DIR__ . '/assets/switch-template.xml');
			$file = $template->getFile();
			self::assertTrue($file->isAvailable());
			self::assertEquals($template->getInstance($this->container), $template = $template->getInstance($this->container));
			$template->template($this->control);
			self::assertEquals('<!DOCTYPE html>
<html attribute="choo" title="poo">
	<head>
		<meta charset="utf-8">
		<title></title>
	</head>
	<body>
		<div>
			<div class="the-second-bar"></div>
			<div class="dummy-div">
				<div class="the-second"></div>
			</div>
		</div>
	</body>
</html>
', $this->control->render());
		}

		public function testButton() {
			$template = $this->templateManager->template(__DIR__ . '/assets/button.xml');
			$file = $template->getFile();
			self::assertTrue($file->isAvailable());
			self::assertEquals($template->getInstance($this->container), $template = $template->getInstance($this->container));
			$template->template($this->control);
			self::assertEquals('<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<title></title>
	</head>
	<body>
		<div class="button edde-clickable" data-control="TestDocument" data-action="OnUpdate">foo</div>
	</body>
</html>
', $this->control->render());
		}

		public function testIdBind() {
			$template = $this->templateManager->template(__DIR__ . '/assets/id.xml');
			$file = $template->getFile();
			self::assertTrue($file->isAvailable());
			self::assertEquals($template->getInstance($this->container), $template = $template->getInstance($this->container));
			$template->template($this->control);
			self::assertEquals('<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<title></title>
	</head>
	<body>
		<div id="blabla"></div>
		<div class="button edde-clickable" data-bind="blabla" data-control="TestDocument" data-action="Foo"></div>
	</body>
</html>
', $this->control->render());
		}

		protected function setUp() {
			$this->resourceManager = new ResourceManager();
			$this->resourceManager->registerResourceHandler(new XmlResourceHandler(new XmlParser()));
			$this->container = $container = ContainerFactory::create([
				IResourceManager::class => $this->resourceManager,
				ITemplateDirectory::class => function () {
					return new TemplateDirectory(__DIR__ . '/temp');
				},
				\TestDocument::class,
				IncludeNodeMacro::class,
				SwitchNodeMacro::class,
				BindIdAttributeMacro::class,
				ICryptEngine::class => CryptEngine::class,
				IStyleSheetCompiler::class => StyleSheetCompiler::class,
				IJavaScriptCompiler::class => JavaScriptCompiler::class,
			]);
			$this->templateManager = $this->container->create(TemplateManager::class);
			$this->templateManager->onSetup(function (ITemplateManager $templateManager) use ($container) {
				$templateManager->registerMacro(new ControlMacro());
				$templateManager->registerMacro(new DivNodeMacro());
				$templateManager->registerMacro(new CssNodeMacro());
				$templateManager->registerMacro(new JsNodeMacro());
				$templateManager->registerMacro(new ButtonNodeMacro());
				$templateManager->registerMacro($container->create(SwitchNodeMacro::class));
				$templateManager->registerMacro($container->create(IncludeNodeMacro::class));
				$templateManager->registerMacro($container->create(BindIdAttributeMacro::class));
			});
			$this->control = $this->container->create(\TestDocument::class);
		}
	}


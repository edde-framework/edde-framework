<?php
	declare(strict_types = 1);

	namespace Edde\Common\Template;

	use Edde\Api\Container\IContainer;
	use Edde\Api\Crypt\ICryptEngine;
	use Edde\Api\File\IRootDirectory;
	use Edde\Api\Html\IHtmlControl;
	use Edde\Api\Resource\IResourceManager;
	use Edde\Api\Template\ITemplateDirectory;
	use Edde\Api\Template\ITemplateManager;
	use Edde\Api\Web\IJavaScriptCompiler;
	use Edde\Api\Web\IStyleSheetCompiler;
	use Edde\Common\Crypt\CryptEngine;
	use Edde\Common\File\RootDirectory;
	use Edde\Common\Resource\ResourceManager;
	use Edde\Common\Template\Macro\Control\BindIdAttributeMacro;
	use Edde\Common\Template\Macro\Control\ButtonMacro;
	use Edde\Common\Template\Macro\Control\ControlMacro;
	use Edde\Common\Template\Macro\Control\CssMacro;
	use Edde\Common\Template\Macro\Control\DivMacro;
	use Edde\Common\Template\Macro\Control\JsMacro;
	use Edde\Common\Template\Macro\Control\PasswordMacro;
	use Edde\Common\Template\Macro\Control\SchemaMacro;
	use Edde\Common\Template\Macro\Control\SpanMacro;
	use Edde\Common\Template\Macro\Control\TextMacro;
	use Edde\Common\Template\Macro\IncludeMacro;
	use Edde\Common\Template\Macro\LoopMacro;
	use Edde\Common\Template\Macro\SwitchMacro;
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
			$template = $this->templateManager->template(__DIR__ . '/assets/template/switch-template.xml');
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
			<div class="the-second-bar">lorem ipsum</div>
			<div class="dummy-div">
				<div class="the-second">bar content</div>
			</div>
		</div>
	</body>
</html>
', $this->control->render());
		}

		public function testButton() {
			$template = $this->templateManager->template(__DIR__ . '/assets/template/button.xml');
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
			$template = $this->templateManager->template(__DIR__ . '/assets/template/id.xml');
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
		<div id="blabla" data-schema="Foo\Bar\Schema" data-property="bar"></div>
		<div class="button edde-clickable" data-bind="blabla" data-control="TestDocument" data-action="Foo"></div>
	</body>
</html>
', $this->control->render());
		}

		public function testInput() {
			$template = $this->templateManager->template(__DIR__ . '/assets/template/input.xml');
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
		<input class="edde-value edde-text-input" type="text" value="" data-schema="poo" data-property="text">
		<input class="edde-value edde-text-input class-here" type="password" data-schema="poo" data-property="password">
	</body>
</html>
', $this->control->render());
		}

		public function testSpan() {
			$template = $this->templateManager->template(__DIR__ . '/assets/template/span.xml');
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
		<div class="foo">
			<span data-cheat="yep we will cheat!">some spanish span here</span>
		</div>
	</body>
</html>
', $this->control->render());
		}

		public function testInclude() {
			$template = $this->templateManager->template(__DIR__ . '/assets/template/include.xml');
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
		<div class="simple-div">
			<div>simply included</div>
		</div>
		<span class="another-included">span here</span>
	</body>
</html>
', $this->control->render());
		}

		public function testLoop01() {
			$template = $this->templateManager->template(__DIR__ . '/assets/template/loop-01.xml');
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
		<div class="simple-div">
			<div>simply included</div>
		</div>
		<span class="another-included">span here</span>
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
				IRootDirectory::class => function () {
					return new RootDirectory(__DIR__);
				},
				\TestDocument::class,
				IncludeMacro::class,
				SwitchMacro::class,
				BindIdAttributeMacro::class,
				SchemaMacro::class,
				ICryptEngine::class => CryptEngine::class,
				IStyleSheetCompiler::class => StyleSheetCompiler::class,
				IJavaScriptCompiler::class => JavaScriptCompiler::class,
				TextMacro::class,
				PasswordMacro::class,
			]);
			$this->templateManager = $this->container->create(TemplateManager::class);
			$this->templateManager->onSetup(function (ITemplateManager $templateManager) use ($container) {
				$templateManager->registerMacroList([
					new ControlMacro(),
					new DivMacro(),
					new SpanMacro(),
					new TextMacro(),
					new PasswordMacro(),
					new LoopMacro(),
					new CssMacro(),
					new JsMacro(),
					new ButtonMacro(),
					new SchemaMacro(),
					$container->create(SwitchMacro::class),
					$container->create(IncludeMacro::class),
					$container->create(BindIdAttributeMacro::class),
				]);
			});
			$this->control = $this->container->create(\TestDocument::class);
		}
	}


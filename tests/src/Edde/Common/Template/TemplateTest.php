<?php
	declare(strict_types = 1);

	namespace Edde\Common\Template;

	use Edde\Api\Container\IContainer;
	use Edde\Api\Crypt\ICryptEngine;
	use Edde\Api\File\IRootDirectory;
	use Edde\Api\IAssetsDirectory;
	use Edde\Api\Link\ILinkFactory;
	use Edde\Api\Resource\IResourceManager;
	use Edde\Api\Template\ITemplateDirectory;
	use Edde\Api\Template\ITemplateManager;
	use Edde\Api\Web\IJavaScriptCompiler;
	use Edde\Api\Web\IStyleSheetCompiler;
	use Edde\Api\Xml\IXmlParser;
	use Edde\Common\AssetsDirectory;
	use Edde\Common\Crypt\CryptEngine;
	use Edde\Common\File\RootDirectory;
	use Edde\Common\Html\MacroSet;
	use Edde\Common\Html\TemplateControl;
	use Edde\Common\Link\ControlLinkGenerator;
	use Edde\Common\Link\HostUrl;
	use Edde\Common\Link\LinkFactory;
	use Edde\Common\Resource\ResourceManager;
	use Edde\Common\Template\Macro\Control\ControlMacro;
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
		 * @var \TestDocument
		 */
		protected $control;

		public function testDummy() {
			$template = $this->templateManager->template(__DIR__ . '/assets/template/dummy.xml');
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
	<body></body>
</html>
', $this->control->render());
		}

		public function testSimpleTemplate() {
			$template = $this->templateManager->template(__DIR__ . '/assets/template/simple-template.xml');
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
		<div>
			<div>
				<div class="hidden one"></div>
			</div>
		</div>
		<div>
			<div>with value</div>
			<div>another value</div>
			<div>
				<div class="another hidden"></div>
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
		<div class="button edde-clickable" data-action="https://127.0.0.1/foo?param=foo&control=TestDocument&action=on-update">foo</div>
	</body>
</html>
', $this->control->render());
		}

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

		public function testSwitch2Template() {
			$template = $this->templateManager->template(__DIR__ . '/assets/template/switch2-template.xml');
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
		<div class="button edde-clickable" data-action="https://127.0.0.1/foo?param=foo&control=TestDocument&action=foo" data-bind="blabla"></div>
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

		public function testSpanStrangeAttributes() {
			$template = $this->templateManager->template(__DIR__ . '/assets/template/span-strange-attributes.xml');
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
			<span data-cheat="I\'m really happy here!">some spanish span here</span>
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
		<div class="looping">
			<div>looped-one</div>
		</div>
		<div class="looping">
			<div>another-looop</div>
		</div>
		<div class="another-kind-of-loop">
			<div class="looping2" data-tribute="first">looped-one</div>
			<div class="looping2" data-tribute="second">another-looop</div>
		</div>
	</body>
</html>
', $this->control->render());
		}

		public function testLoop02() {
			$template = $this->templateManager->template(__DIR__ . '/assets/template/loop-02.xml');
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
		<div class="looping">
			<div class="looped-one">
				<div class="simple-div">
					<div>simply included</div>
				</div>
				<span class="another-included">span here</span>
			</div>
		</div>
		<div class="looping">
			<div class="another-looop">
				<div class="simple-div">
					<div>simply included</div>
				</div>
				<span class="another-included">span here</span>
			</div>
		</div>
		<div class="another-kind-of-loop">
			<div class="looping2" data-tribute="first">looped-one</div>
			<div class="looping2" data-tribute="second">another-looop</div>
		</div>
	</body>
</html>
', $this->control->render());
		}

		public function testLoop03() {
			$template = $this->templateManager->template(__DIR__ . '/assets/template/loop-03.xml');
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
		<div class="button edde-clickable looped-one" data-action="https://127.0.0.1/foo?param=foo&control=TestDocument&action=first"></div>
		<div class="button edde-clickable another-looop" data-action="https://127.0.0.1/foo?param=foo&control=TestDocument&action=second"></div>
	</body>
</html>
', $this->control->render());
		}

		public function testLoop04() {
			$template = $this->templateManager->template(__DIR__ . '/assets/template/loop-04.xml');
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
		<div>
			<div>looped-one</div>
			<div class="first"></div>
			<div class="second"></div>
			<div>looped-one</div>
		</div>
		<div>
			<div>another-looop</div>
			<div class="first"></div>
			<div class="second"></div>
			<div>another-looop</div>
		</div>
	</body>
</html>
', $this->control->render());
		}

		public function testLoop05() {
			$template = $this->templateManager->template(__DIR__ . '/assets/template/loop-05.xml');
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
		<div>
			<div>item-value whee</div>
			<div class="first"></div>
			<div class="second"></div>
			<div>another-item-value whee</div>
		</div>
		<div>
			<div>item-value foo</div>
			<div class="first"></div>
			<div class="second"></div>
			<div>another-item-value foo</div>
		</div>
		<div>
			<div>item-value poo</div>
			<div class="first"></div>
			<div class="second"></div>
			<div>another-item-value poo</div>
		</div>
	</body>
</html>
', $this->control->render());
		}

		public function testHeader() {
			$template = $this->templateManager->template(__DIR__ . '/assets/template/header.xml');
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
		<h1>foo</h1>
		<h2>poo</h2>
		<h3>woo</h3>
		<h4>doo</h4>
		<h5>goo</h5>
		<h6>
			<span>spanish header</span>
		</h6>
	</body>
</html>
', $this->control->render());
		}

		public function testPass() {
			$template = $this->templateManager->template(__DIR__ . '/assets/template/pass.xml');
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
		<div class="pass-me special-class">
			<span class="foo bar">some span here</span>
		</div>
		<div class="child-pass">
			<span class="first special-span-class"></span>
			<span class="second special-span-class"></span>
			<span class="third special-span-class"></span>
		</div>
		<div class="child-pass">
			<div class="button edde-clickable first special-button-class" data-action="https://127.0.0.1/foo?param=foo&control=TestDocument&action=foo"></div>
			<div class="button edde-clickable second special-button-class" data-action="https://127.0.0.1/foo?param=foo&control=TestDocument&action=foo"></div>
			<div class="button edde-clickable third special-button-class" data-action="https://127.0.0.1/foo?param=foo&control=TestDocument&action=foo"></div>
		</div>
	</body>
</html>
', $this->control->render());
		}

		public function testCustomControl() {
			$control = $this->container->create(TemplateControl::class);
			$control->setTemplate(__DIR__ . '/assets/template/custom.xml');
			$control->dirty();
			self::assertEquals('	<div class="will-use-custom-control">
			<div class="hello" attr="foo">custom control</div>
	</div>
', $control->render());
		}

		public function testLayout() {
			$template = $this->templateManager->template(__DIR__ . '/assets/template/layout.xml');
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
		<div class="foo"></div>
		<div class="with-block">
			<div>
				<span>lorem ipsum or something like that</span>
			</div>
			<div class="bar"></div>
		</div>
		<div class="foo-bar"></div>
		<div class="something">
			<div class="qwerty"></div>
			<div>
				<span>foo</span>
				<div>
					<div></div>
					<div></div>
					<div class="soo empty div here"></div>
					<div></div>
				</div>
			</div>
		</div>
	</body>
</html>
', $this->control->render());
		}

		protected function setUp() {
			$this->container = ContainerFactory::create([
				IResourceManager::class => ResourceManager::class,
				ITemplateDirectory::class => function () {
					return new TemplateDirectory(__DIR__ . '/temp');
				},
				IRootDirectory::class => function () {
					return new RootDirectory(__DIR__);
				},
				IAssetsDirectory::class => function (IRootDirectory $rootDirectory) {
					return $rootDirectory->directory('assets', AssetsDirectory::class);
				},
				\TestDocument::class,
				ICryptEngine::class => CryptEngine::class,
				IStyleSheetCompiler::class => StyleSheetCompiler::class,
				IJavaScriptCompiler::class => JavaScriptCompiler::class,
				ITemplateManager::class => TemplateManager::class,
				IXmlParser::class => XmlParser::class,
				ILinkFactory::class => function () {
					$linkFactory = new LinkFactory($hostUrl = HostUrl::create('https://127.0.0.1/foo?param=foo'));
					$linkFactory->registerLinkGenerator($controlLinkGenerator = new ControlLinkGenerator());
					$controlLinkGenerator->lazyHostUrl($hostUrl);
					return $linkFactory;
				},
			]);
			$this->resourceManager = $this->container->create(IResourceManager::class);
			$this->resourceManager->registerResourceHandler($this->container->create(XmlResourceHandler::class));
			$this->templateManager = $this->container->create(ITemplateManager::class);
			$this->templateManager->onSetup(function (ITemplateManager $templateManager) {
				$templateManager->registerMacroList(array_merge(MacroSet::macroList($this->container), [
					new ControlMacro('custom-control', \CustomControl::class),
					$this->container->inject(new IncludeMacro()),
					$this->container->inject(new SwitchMacro()),
					$this->container->inject(new LoopMacro()),
				]));
			});
			$this->control = $this->container->create(\TestDocument::class);
		}
	}


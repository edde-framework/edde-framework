<?php
	declare(strict_types = 1);

	namespace Edde\Common\Template;

	use Edde\Api\Container\IContainer;
	use Edde\Api\Resource\IResourceManager;
	use Edde\Api\Template\ITemplate;
	use Edde\Common\Html\ButtonControl;
	use Edde\Common\Html\DivControl;
	use Edde\Common\Html\Document\DocumentControl;
	use Edde\Common\Html\Value\PasswordInputControl;
	use Edde\Common\Html\Value\TextInputControl;
	use Edde\Common\Resource\ResourceManager;
	use Edde\Common\Template\Filter\ActionAttributeFilter;
	use Edde\Common\Template\Filter\BindAttributeFilter;
	use Edde\Common\Template\Filter\ClassAttributeFilter;
	use Edde\Common\Template\Filter\PropertyAttributeFilter;
	use Edde\Common\Template\Filter\ValueAttributeFilter;
	use Edde\Common\Template\Macro\ControlMacro;
	use Edde\Common\Template\Macro\HtmlMacro;
	use Edde\Common\Template\Macro\IncludeMacro;
	use Edde\Common\Template\Macro\SchemaMacro;
	use Edde\Common\Xml\XmlParser;
	use Edde\Common\Xml\XmlResourceHandler;
	use Edde\Ext\Container\ContainerFactory;
	use phpunit\framework\TestCase;

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
		 * @var DocumentControl
		 */
		protected $documentControl;
		/**
		 * @var ITemplate
		 */
		protected $template;

		public function testSimpleTemplate() {
			$this->template->macro($this->resourceManager->file(__DIR__ . '/assets/simple-template.xml'), $this->documentControl);
			self::assertEquals('<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<title></title>
	</head>
	<body>
		<div class="foo bar"></div>
	</body>
</html>
', $this->documentControl->render());
		}

		public function testLessSimpleTemplate() {
			$this->template->macro($this->resourceManager->file(__DIR__ . '/assets/less-simple-template.xml'), $this->documentControl);
			self::assertEquals('<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<title></title>
	</head>
	<body>
		<div class="foo bar"></div>
		<div id="some-group-of-data" class="foo-bar">
			<div class="poo">
				<div class="tasty-poo"></div>
				<div class="less-tasty-poo"></div>
				<div class="big-poo"></div>
			</div>
			<div class="button edde-clickable small-cute-button" data-control="Edde\Common\Html\Document\DocumentControl" data-action="onClick" data-bind="some-group-of-data" title="click me!">button title</div>
		</div>
	</body>
</html>
', $this->documentControl->render());
		}

		public function testInput() {
			$this->template->macro($this->resourceManager->file(__DIR__ . '/assets/input.xml'), $this->documentControl);
			self::assertEquals('<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<title></title>
	</head>
	<body>
		<input class="edde-value edde-text-input" type="text" value="" data-schema="Foo\Bar\LoginSchema" data-property="login">
		<input class="edde-value edde-text-input" type="password" data-schema="Foo\Bar\LoginSchema" data-property="password">
	</body>
</html>
', $this->documentControl->render());
		}

		public function testIncludeAlpha() {
			$this->template->setVariable('variable', __DIR__ . '/assets/snippet-alpha.xml');
			$this->template->macro($this->resourceManager->file(__DIR__ . '/assets/include.xml'), $this->documentControl);
			self::assertEquals('<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<title></title>
	</head>
	<body>
		<div class="static-one"></div>
		<div class="alpha"></div>
	</body>
</html>
', $this->documentControl->render());
		}

		public function testIncludeBeta() {
			$this->template->setVariable('variable', __DIR__ . '/assets/snippet-beta.xml');
			$this->template->macro($this->resourceManager->file(__DIR__ . '/assets/include.xml'), $this->documentControl);
			self::assertEquals('<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<title></title>
	</head>
	<body>
		<div class="static-one"></div>
		<div class="beta"></div>
	</body>
</html>
', $this->documentControl->render());
		}

		protected function setUp() {
			$this->resourceManager = new ResourceManager();
			$this->resourceManager->registerResourceHandler(new XmlResourceHandler(new XmlParser()));
			$this->container = ContainerFactory::create();
			$this->documentControl = new DocumentControl();
			$this->documentControl->injectContainer($this->container);
			$this->template = $template = new Template();
			$template->registerMacro(new HtmlMacro());
			$template->registerMacro($schemaMacro = new SchemaMacro());
			$template->registerMacro(new IncludeMacro($this->resourceManager));
			$template->registerMacro($controlMacro = new ControlMacro($this->container));
			$controlMacro->registerControlList([
				'div' => DivControl::class,
				'button' => ButtonControl::class,
				'text' => TextInputControl::class,
				'password' => PasswordInputControl::class,
			]);
			$controlMacro->registerFilterList([
				'class' => new ClassAttributeFilter(),
				'action' => new ActionAttributeFilter(),
				'value' => new ValueAttributeFilter(),
				'property' => new PropertyAttributeFilter($schemaMacro),
				'bind' => new BindAttributeFilter(),
			]);
		}
	}


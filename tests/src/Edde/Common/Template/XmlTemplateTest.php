<?php
	declare(strict_types = 1);

	namespace Edde\Common\Template;

	use Edde\Api\Container\IContainer;
	use Edde\Api\Template\ITemplate;
	use Edde\Common\Html\Document\DocumentControl;
	use Edde\Common\Resource\ResourceManager;
	use Edde\Common\Schema\SchemaFactory;
	use Edde\Common\Schema\SchemaManager;
	use Edde\Common\Xml\XmlParser;
	use Edde\Common\Xml\XmlResourceHandler;
	use Edde\Ext\Container\ContainerFactory;
	use Foo\Bar\LoginSchema;
	use phpunit\framework\TestCase;

	require_once(__DIR__ . '/assets/schema.php');

	class XmlTemplateTest extends TestCase {
		/**
		 * @var ITemplate
		 */
		protected $template;
		/**
		 * @var IContainer
		 */
		protected $container;
		/**
		 * @var DocumentControl
		 */
		protected $documentControl;

		public function testSimpleTemplate() {
			$this->template->build(__DIR__ . '/assets/simple-template.xml', $this->documentControl->getBody());
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
			$this->template->build(__DIR__ . '/assets/less-simple-template.xml', $this->documentControl->getBody());
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
			<div class="button edde-clickable small-cute-button" data-control="Edde\Common\Html\Document\DocumentControl" data-action="onClick" title="click me!" data-bind="some-group-of-data">button title</div>
		</div>
	</body>
</html>
', $this->documentControl->render());
		}

		public function testInput() {
			$this->template->build(__DIR__ . '/assets/input.xml', $this->documentControl->getBody());
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

		protected function setUp() {
			$this->template = new XmlTemplate($this->container = ContainerFactory::create(), $resourceManager = new ResourceManager(), $schemaManager = new SchemaManager(new SchemaFactory(new ResourceManager())));
			$resourceManager->registerResourceHandler(new XmlResourceHandler(new XmlParser()));
			$schemaManager->addSchema(new LoginSchema(LoginSchema::class));
			$this->documentControl = new DocumentControl();
			$this->documentControl->injectContainer($this->container);
		}
	}

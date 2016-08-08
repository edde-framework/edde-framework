<?php
	declare(strict_types = 1);

	namespace Edde\Common\Template;

	use Edde\Api\Container\IContainer;
	use Edde\Api\Template\ITemplate;
	use Edde\Common\Html\Document\DocumentControl;
	use Edde\Common\Resource\ResourceManager;
	use Edde\Common\Xml\XmlParser;
	use Edde\Common\Xml\XmlResourceHandler;
	use Edde\Ext\Container\ContainerFactory;
	use phpunit\framework\TestCase;

	class XmlTemplateTest extends TestCase {
		/**
		 * @var ITemplate
		 */
		protected $template;
		/**
		 * @var IContainer
		 */
		protected $container;

		public function testSimpleTemplate() {
			$control = new DocumentControl();
			$control->injectContainer($this->container);
			$this->template->build(__DIR__ . '/assets/simple-template.xml', $control->getBody());
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
', $control->render());
		}

		public function testLessSimpleTemplate() {
			$control = new DocumentControl();
			$control->injectContainer($this->container);
			$this->template->build(__DIR__ . '/assets/less-simple-template.xml', $control->getBody());
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
			<div class="small-cute-button" title="click me!" data-control="Edde\Common\Html\Document\DocumentControl" data-action="onClick" data-bind="some-group-of-data">button title</div>
		</div>
	</body>
</html>
', $control->render());
		}

		protected function setUp() {
			$this->template = new XmlTemplate($this->container = ContainerFactory::create(), $resourceManager = new ResourceManager());
			$resourceManager->registerResourceHandler(new XmlResourceHandler(new XmlParser()));
		}
	}

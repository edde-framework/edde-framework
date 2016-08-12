<?php
	declare(strict_types = 1);

	namespace Edde\Common\Template;

	use Edde\Api\Container\IContainer;
	use Edde\Api\Html\IHtmlControl;
	use Edde\Api\Resource\IResourceManager;
	use Edde\Api\Template\ITemplateDirectory;
	use Edde\Api\Template\ITemplateManager;
	use Edde\Common\Resource\ResourceManager;
	use Edde\Common\Template\Macro\ControlMacro;
	use Edde\Common\Template\Macro\DivNodeMacro;
	use Edde\Common\Template\Macro\IncludeMacro;
	use Edde\Common\Template\Macro\SwitchMacro;
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

		protected function setUp() {
			$this->resourceManager = new ResourceManager();
			$this->resourceManager->registerResourceHandler(new XmlResourceHandler(new XmlParser()));
			$this->container = $container = ContainerFactory::create([
				IResourceManager::class => $this->resourceManager,
				ITemplateDirectory::class => function () {
					return new TemplateDirectory(__DIR__ . '/temp');
				},
				\TestDocument::class,
				IncludeMacro::class,
			]);
			$this->templateManager = $this->container->create(TemplateManager::class);
			$this->templateManager->onSetup(function (ITemplateManager $templateManager) use ($container) {
				$templateManager->registerMacro(new ControlMacro());
				$templateManager->registerMacro(new DivNodeMacro());
				$templateManager->registerMacro(new SwitchMacro());
				$templateManager->registerMacro($container->create(IncludeMacro::class));
			});
			$this->control = $this->container->create(\TestDocument::class);
		}
	}


<?php
	declare(strict_types = 1);

	namespace Edde\Common\Template;

	use Edde\Api\Container\IContainer;
	use Edde\Api\Resource\IResourceManager;
	use Edde\Api\Template\ITemplateManager;
	use Edde\Common\Html\Document\DocumentControl;
	use Edde\Common\Resource\ResourceManager;
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
		 * @var ITemplateManager
		 */
		protected $templateManager;

		public function testSwitchTemplate() {
			$class = $this->templateManager->template(__DIR__ . '/assets/switch-template.xml');
		}

		protected function setUp() {
			$this->resourceManager = new ResourceManager();
			$this->resourceManager->registerResourceHandler(new XmlResourceHandler(new XmlParser()));
			$this->container = ContainerFactory::create([
				IResourceManager::class => $this->resourceManager,
			]);
			$this->documentControl = new DocumentControl();
			$this->documentControl->injectContainer($this->container);
			$this->templateManager = $this->container->create(TemplateManager::class);
		}
	}


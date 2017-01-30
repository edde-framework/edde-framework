<?php
	declare(strict_types=1);

	namespace Edde\Common\Template;

	use Edde\Api\Container\IContainer;
	use Edde\Api\Converter\IConverterManager;
	use Edde\Api\File\IRootDirectory;
	use Edde\Api\Template\ITemplateManager;
	use Edde\Common\File\RootDirectory;
	use Edde\Ext\Container\ClassFactory;
	use Edde\Ext\Container\ContainerFactory;
	use Edde\Ext\Converter\ConverterManagerConfigurator;
	use PHPUnit\Framework\TestCase;

	class TemplateManagerTest extends TestCase {
		/**
		 * @var IContainer
		 */
		protected $container;
		/**
		 * @var ITemplateManager
		 */
		protected $templateManager;

		public function testSimpleTemplate() {
//			$this->templateManager->registerTemplateProvider($this->container->create());
			$this->templateManager->compile([
				'layout',
				'some-content',
			]);
		}

		protected function setUp() {
			$this->container = ContainerFactory::container([
				IRootDirectory::class => new RootDirectory(__DIR__),
				ITemplateManager::class => TemplateManager::class,
				new ClassFactory(),
			], [
				IConverterManager::class => ConverterManagerConfigurator::class,
			]);
			$this->templateManager = $this->container->create(ITemplateManager::class);
		}
	}

<?php
	declare(strict_types=1);

	namespace Edde\Common\Template;

	use Edde\Api\Container\IContainer;
	use Edde\Api\Converter\IConverterManager;
	use Edde\Api\File\IRootDirectory;
	use Edde\Api\Resource\IResourceManager;
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

		public function testSimpleTemplate() {
			/**
			 * @var $resourceManager IResourceManager
			 */
			$resourceManager = $this->container->create(IResourceManager::class);
			$node = $resourceManager->file(__DIR__ . '/assets/layout.xhtml');
		}

		protected function setUp() {
			$this->container = ContainerFactory::container([
				IRootDirectory::class => new RootDirectory(__DIR__),
				new ClassFactory(),
			], [
				IConverterManager::class => ConverterManagerConfigurator::class,
			]);
		}
	}

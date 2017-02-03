<?php
	declare(strict_types=1);

	namespace Edde\Common\Template;

	use Edde\Api\Cache\ICacheStorage;
	use Edde\Api\Container\IContainer;
	use Edde\Api\Converter\IConverterManager;
	use Edde\Api\File\IRootDirectory;
	use Edde\Api\Template\ITemplate;
	use Edde\Api\Template\ITemplateManager;
	use Edde\Common\File\RootDirectory;
	use Edde\Ext\Cache\InMemoryCacheStorage;
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
		 * @var IRootDirectory
		 */
		protected $rootDirectory;
		/**
		 * @var ITemplateManager
		 */
		protected $templateManager;

		public function testException() {
			$this->expectException(UnknownTemplateException::class);
			$this->expectExceptionMessage('Requested template name [foo-bar] cannot be found; there are no template providers - please register instance of [Edde\Api\Template\ITemplateProvider].');
			$this->templateManager->template([
				'foo-bar',
			]);
		}

		public function testException2() {
			$this->expectException(UnknownTemplateException::class);
			$this->expectExceptionMessage('Requested template name [foo-bar] cannot be found.');
			$this->templateManager->registerTemplateProvider($this->container->create(DirectoryTemplateProvider::class, [$this->rootDirectory]));
			$this->templateManager->template([
				'foo-bar',
			]);
		}

		public function testTemplate() {
			$this->templateManager->registerTemplateProvider($this->container->create(DirectoryTemplateProvider::class, [$this->rootDirectory]));
			$template = $this->templateManager->template([
				'layout',
				'here-is-hidden-content-of-the-fucking-template',
			]);
			$file = $template->compile();
		}

		protected function setUp() {
			$this->container = ContainerFactory::container([
				IRootDirectory::class => $this->rootDirectory = new RootDirectory(__DIR__),
				ITemplateManager::class => TemplateManager::class,
				ITemplate::class => Template::class,
				ICacheStorage::class => InMemoryCacheStorage::class,
				new ClassFactory(),
			], [
				IConverterManager::class => ConverterManagerConfigurator::class,
			]);
			$this->rootDirectory->normalize();
			$this->templateManager = $this->container->create(ITemplateManager::class);
		}
	}

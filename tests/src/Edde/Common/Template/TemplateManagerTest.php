<?php
	declare(strict_types = 1);

	namespace Edde\Common\Template;

	use Edde\Api\Converter\IConverterManager;
	use Edde\Api\Crypt\ICryptEngine;
	use Edde\Api\File\IRootDirectory;
	use Edde\Api\Resource\IResourceManager;
	use Edde\Api\Template\ITemplateManager;
	use Edde\Api\Xml\IXmlParser;
	use Edde\Common\Converter\ConverterManager;
	use Edde\Common\Crypt\CryptEngine;
	use Edde\Common\File\RootDirectory;
	use Edde\Common\Resource\ResourceManager;
	use Edde\Common\Xml\XmlParser;
	use Edde\Ext\Container\ContainerFactory;
	use Edde\Ext\Converter\XmlConverter;
	use phpunit\framework\TestCase;

	class TemplateManagerTest extends TestCase {
		/**
		 * @var ITemplateManager
		 */
		protected $templateManager;

		public function testCommon() {
			$template = $this->templateManager->template(__DIR__ . '/template/complex/layout.xml');
		}

		protected function setUp() {
			$container = ContainerFactory::create([
				ITemplateManager::class => TemplateManager::class,
				IResourceManager::class => ResourceManager::class,
				IConverterManager::class => ConverterManager::class,
				IXmlParser::class => XmlParser::class,
				IRootDirectory::class => new RootDirectory(__DIR__ . '/temp'),
				ICryptEngine::class => CryptEngine::class,
			]);
			/** @var $converterManager IConverterManager */
			$converterManager = $container->create(IConverterManager::class);
			$converterManager->registerConverter($container->inject(new XmlConverter()));
			$this->templateManager = $container->create(ITemplateManager::class);
			$this->templateManager->onSetup(function (ITemplateManager $templateManager) {
			});
		}
	}

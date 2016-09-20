<?php
	declare(strict_types = 1);

	namespace Edde\Common\Template;

	use Edde\Api\Container\IContainer;
	use Edde\Api\Converter\IConverterManager;
	use Edde\Api\Resource\IResourceManager;
	use Edde\Api\Template\ICompiler;
	use Edde\Api\Xml\IXmlParser;
	use Edde\Common\Converter\ConverterManager;
	use Edde\Common\File\File;
	use Edde\Common\Resource\ResourceManager;
	use Edde\Common\Xml\XmlParser;
	use Edde\Ext\Container\ContainerFactory;
	use Edde\Ext\Converter\XmlConverter;
	use phpunit\framework\TestCase;

	class CompilerTest extends TestCase {
		/**
		 * @var IContainer
		 */
		protected $container;

		public function testCompile() {
			/** @var $compiler ICompiler */
			$compiler = $this->container->inject(new Compiler(new File(__DIR__ . '/template/complex/layout.xml'), new File(__DIR__ . '/temp/complex-template.php')));
			$compiler->template();
		}

		protected function setUp() {
			$this->container = ContainerFactory::create([
				IResourceManager::class => ResourceManager::class,
				IConverterManager::class => ConverterManager::class,
				IXmlParser::class => XmlParser::class,
			]);
			/** @var $converterManager IConverterManager */
			$converterManager = $this->container->create(IConverterManager::class);
			$converterManager->registerConverter($this->container->inject(new XmlConverter()));
		}
	}

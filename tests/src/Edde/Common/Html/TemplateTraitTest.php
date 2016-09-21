<?php
	declare(strict_types = 1);

	namespace Edde\Common\Html;

	use Edde\Api\Application\IRequest;
	use Edde\Api\Container\IContainer;
	use Edde\Api\Converter\IConverterManager;
	use Edde\Api\Crypt\ICryptEngine;
	use Edde\Api\File\IRootDirectory;
	use Edde\Api\Resource\IResourceManager;
	use Edde\Api\Template\IMacroSet;
	use Edde\Api\Template\ITemplateManager;
	use Edde\Api\Xml\IXmlParser;
	use Edde\Common\Application\Request;
	use Edde\Common\Converter\ConverterManager;
	use Edde\Common\Crypt\CryptEngine;
	use Edde\Common\File\RootDirectory;
	use Edde\Common\Resource\ResourceManager;
	use Edde\Common\Template\TemplateManager;
	use Edde\Common\Xml\XmlParser;
	use Edde\Ext\Container\ContainerFactory;
	use Edde\Ext\Converter\XmlConverter;
	use Edde\Ext\Template\DefaultMacroSet;
	use phpunit\framework\TestCase;

	require_once(__DIR__ . '/assets/assets.php');

	class TemplateTraitTest extends TestCase {
		/**
		 * @var IContainer
		 */
		protected $container;

		public function testTemplateTrait() {
			$this->container->inject($control = new \SomeTemplatedControl());
			/**
			 * template is automagically using layout.xml template
			 */
			$control->template();
			$control->dirty();
			self::assertEquals('<div class="foo-bar"></div>
<span>hello world!</span>
<div class="bar-foo"></div>
', $control->render());
		}

		protected function setUp() {
			$this->container = ContainerFactory::create([
				ITemplateManager::class => TemplateManager::class,
				IResourceManager::class => ResourceManager::class,
				IConverterManager::class => ConverterManager::class,
				IXmlParser::class => XmlParser::class,
				IRootDirectory::class => new RootDirectory(__DIR__ . '/temp'),
				ICryptEngine::class => CryptEngine::class,
				IMacroSet::class => function (IContainer $container) {
					return DefaultMacroSet::factory($container);
				},
				IRequest::class => function () {
					return new Request('foo', 'bar', 'method', []);
				},
			]);
			$converterManager = $this->container->create(IConverterManager::class);
			$converterManager->registerConverter($this->container->inject(new XmlConverter()));
		}
	}

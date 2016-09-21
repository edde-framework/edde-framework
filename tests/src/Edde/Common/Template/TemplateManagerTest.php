<?php
	declare(strict_types = 1);

	namespace Edde\Common\Template;

	use Edde\Api\Container\IContainer;
	use Edde\Api\Converter\IConverterManager;
	use Edde\Api\Crypt\ICryptEngine;
	use Edde\Api\File\IRootDirectory;
	use Edde\Api\Resource\IResourceManager;
	use Edde\Api\Template\IMacroSet;
	use Edde\Api\Template\ITemplateManager;
	use Edde\Api\Xml\IXmlParser;
	use Edde\Common\Converter\ConverterManager;
	use Edde\Common\Crypt\CryptEngine;
	use Edde\Common\File\RootDirectory;
	use Edde\Common\Html\AbstractHtmlTemplate;
	use Edde\Common\Html\Tag\DivControl;
	use Edde\Common\Resource\ResourceManager;
	use Edde\Common\Xml\XmlParser;
	use Edde\Ext\Container\ContainerFactory;
	use Edde\Ext\Converter\XmlConverter;
	use Edde\Ext\Template\DefaultMacroSet;
	use phpunit\framework\TestCase;

	class TemplateManagerTest extends TestCase {
		/**
		 * @var IContainer
		 */
		protected $container;
		/**
		 * @var ITemplateManager
		 */
		protected $templateManager;

		public function testCommon() {
			$file = $this->templateManager->template(__DIR__ . '/template/complex/layout.xml', [
				__DIR__ . '/template/complex/to-be-used.xml',
			]);
			$template = AbstractHtmlTemplate::template($file, $this->container);
			$template->snippet($div = new DivControl());
			$div->addClass('root');
			$div->dirty();
			self::assertEquals('<div class="root">
	<div>
		<div class="first">
			<div class="hidden div"></div>
		</div>
		<div class="second"></div>
		<div class="third">
			<div class="inner-one"></div>
			<div class="inner-two"></div>
			<div class="inner-three"></div>
			<div class="inner-four">
				<div class="deep-one"></div>
			</div>
		</div>
	</div>
	<div class="really-deep-div-here">
		<div class="deepness-of-a-deep">foo</div>
	</div>
	<div class="really-deep-div-here">
		<div class="deepness-of-a-deep">foo</div>
	</div>
	<div class="poo-class">poo</div>
	<div class="used-div-here"></div>
</div>
', $div->render());
		}

		protected function setUp() {
			$this->container = $container = ContainerFactory::create([
				ITemplateManager::class => TemplateManager::class,
				IResourceManager::class => ResourceManager::class,
				IConverterManager::class => ConverterManager::class,
				IXmlParser::class => XmlParser::class,
				IRootDirectory::class => new RootDirectory(__DIR__ . '/temp'),
				ICryptEngine::class => CryptEngine::class,
				IMacroSet::class => function (IContainer $container) {
					return DefaultMacroSet::factory($container);
				},
			]);
			/** @var $converterManager IConverterManager */
			$converterManager = $container->create(IConverterManager::class);
			$converterManager->registerConverter($container->inject(new XmlConverter()));
			$this->templateManager = $container->create(ITemplateManager::class);
			$this->templateManager->onSetup(function (ITemplateManager $templateManager) {
			});
		}
	}

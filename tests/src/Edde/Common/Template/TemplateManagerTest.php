<?php
	declare(strict_types = 1);

	namespace Edde\Common\Template;

	use Edde\Api\Container\IContainer;
	use Edde\Api\Converter\IConverterManager;
	use Edde\Api\Crypt\ICryptEngine;
	use Edde\Api\File\IRootDirectory;
	use Edde\Api\File\ITempDirectory;
	use Edde\Api\Http\IHostUrl;
	use Edde\Api\Link\ILinkFactory;
	use Edde\Api\Resource\IResourceList;
	use Edde\Api\Resource\IResourceManager;
	use Edde\Api\Template\IMacroSet;
	use Edde\Api\Template\ITemplateManager;
	use Edde\Api\Web\IJavaScriptCompiler;
	use Edde\Api\Web\IStyleSheetCompiler;
	use Edde\Api\Xml\IXmlParser;
	use Edde\Common\Converter\ConverterManager;
	use Edde\Common\Crypt\CryptEngine;
	use Edde\Common\File\File;
	use Edde\Common\File\RootDirectory;
	use Edde\Common\File\TempDirectory;
	use Edde\Common\Html\AbstractHtmlTemplate;
	use Edde\Common\Html\Tag\DivControl;
	use Edde\Common\Http\HostUrl;
	use Edde\Common\Link\ControlLinkGenerator;
	use Edde\Common\Link\LinkFactory;
	use Edde\Common\Resource\ResourceList;
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
		/**
		 * @var IResourceList
		 */
		protected $styleSheetList;
		/**
		 * @var IResourceList
		 */
		protected $javaScriptList;

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
	<div data-class="Edde.Common.Html.Tag.ButtonControl" class="button" data-action="http://localhost/foo/bar?a=1&control=Edde%5CCommon%5CHtml%5CTag%5CDivControl&action=action-on-the-root"></div>
	<div data-class="Edde.Common.Html.Tag.ButtonControl" class="button" data-action="http://localhost/foo/bar?a=1&control=Edde%5CCommon%5CHtml%5CTag%5CButtonControl&action=%40action-on-the-current-contol"></div>
	<div data-class="Edde.Common.Html.Tag.ButtonControl" class="button just-useless-button-here"></div>
</div>
', $div->render());
			$cssList = [
				(new File(__DIR__ . '/template/complex/foo/bar/boo.css'))->getUrl()
					->getAbsoluteUrl(),
			];
			$pathList = $this->styleSheetList->getPathList();
			sort($cssList);
			sort($pathList);
			self::assertEquals($cssList, $pathList);
			/**
			 * contorls can generate javascript with proprietary path, so it is not easy to test it
			 *
			 * at least this is most simple test for executed javascript
			 *
			 * 2 is for 1 button and 1 for explicit js macro
			 */
			self::assertCount(2, $this->javaScriptList->getPathList());
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
				ITempDirectory::class => function (IRootDirectory $rootDirectory) {
					return new TempDirectory($rootDirectory->getDirectory());
				},
				IStyleSheetCompiler::class => ResourceList::class,
				IJavaScriptCompiler::class => ResourceList::class,
				IHostUrl::class => HostUrl::create('http://localhost/foo/bar?a=1'),
				ILinkFactory::class => function (IContainer $container, IHostUrl $hostUrl) {
					$linkFactory = new LinkFactory($hostUrl);
					$linkFactory->registerLinkGenerator($container->inject(new ControlLinkGenerator()));
					return $linkFactory;
				},
			]);
			/** @var $converterManager IConverterManager */
			$converterManager = $container->create(IConverterManager::class);
			$converterManager->registerConverter($container->inject(new XmlConverter()));
			$this->templateManager = $container->create(ITemplateManager::class);
			$this->styleSheetList = $container->create(IStyleSheetCompiler::class);
			$this->javaScriptList = $container->create(IJavaScriptCompiler::class);
		}
	}

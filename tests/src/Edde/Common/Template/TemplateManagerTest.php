<?php
	declare(strict_types = 1);

	namespace Edde\Common\Template;

	use Edde\Api\Container\IContainer;
	use Edde\Api\Converter\IConverterManager;
	use Edde\Api\Crypt\ICryptEngine;
	use Edde\Api\File\IRootDirectory;
	use Edde\Api\File\ITempDirectory;
	use Edde\Api\Http\IHostUrl;
	use Edde\Api\IAssetsDirectory;
	use Edde\Api\Link\ILinkFactory;
	use Edde\Api\Resource\IResourceList;
	use Edde\Api\Resource\IResourceManager;
	use Edde\Api\Template\IHelperSet;
	use Edde\Api\Template\IMacroSet;
	use Edde\Api\Template\ITemplateManager;
	use Edde\Api\Web\IJavaScriptCompiler;
	use Edde\Api\Web\IStyleSheetCompiler;
	use Edde\Api\Xml\IXmlParser;
	use Edde\Common\AssetsDirectory;
	use Edde\Common\Converter\ConverterManager;
	use Edde\Common\Crypt\CryptEngine;
	use Edde\Common\File\File;
	use Edde\Common\File\RootDirectory;
	use Edde\Common\File\TempDirectory;
	use Edde\Common\Html\AbstractHtmlTemplate;
	use Edde\Common\Html\Macro\HtmlMacro;
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

	require_once __DIR__ . '/assets.php';

	/**
	 * Test covering all template features from "real world" usage.
	 */
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
		protected $flag = false;

		public function call() {
			$this->flag = true;
		}

		public function testCommon() {
			$file = $this->templateManager->template(__DIR__ . '/template/complex/layout.xml', [
				__DIR__ . '/template/complex/to-be-used.xml',
			]);
			$template = AbstractHtmlTemplate::template($file, $this->container);
			$template->snippet($this->container->inject($div = new \SomeCoolControl()));
			$div->addClass('root');
			$div->dirty();
			self::assertEquals(file_get_contents(__DIR__ . '/template/complex/result.xml'), $div->render());
			$cssList = [
				(new File(__DIR__ . '/../../../../../src/Edde/assets/css/foundation.min.css'))->getUrl()
					->getAbsoluteUrl(),
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
				IAssetsDirectory::class => new AssetsDirectory(__DIR__ . '/../../../../../src/Edde/assets'),
				ICryptEngine::class => CryptEngine::class,
				'\SomeService\From\Container' => $this,
				IMacroSet::class => function (IContainer $container) {
					$macroSet = DefaultMacroSet::macroSet($container);
					$macroSet->onSetup(function (IMacroSet $macroSet) use ($container) {
						$macroSet->registerMacro($container->inject(new HtmlMacro('custom-control', \AnotherCoolControl::class)));
					});
					return $macroSet;
				},
				IHelperSet::class => function (IContainer $container) {
					return DefaultMacroSet::helperSet($container);
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
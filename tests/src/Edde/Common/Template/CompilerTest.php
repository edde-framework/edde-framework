<?php
	declare(strict_types = 1);

	namespace Edde\Common\Template;

	use Edde\Api\Container\IContainer;
	use Edde\Api\Converter\IConverterManager;
	use Edde\Api\Crypt\ICryptEngine;
	use Edde\Api\File\IFile;
	use Edde\Api\File\IRootDirectory;
	use Edde\Api\File\ITempDirectory;
	use Edde\Api\Http\IHostUrl;
	use Edde\Api\IAssetsDirectory;
	use Edde\Api\Link\ILinkFactory;
	use Edde\Api\Resource\IResourceManager;
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

	require_once(__DIR__ . '/assets.php');

	class CompilerTest extends TestCase {
		/**
		 * @var IContainer
		 */
		protected $container;

		public function testComplex() {
			$this->container->inject($compiler = new Compiler(new File(__DIR__ . '/template/complex/layout.xml')));
			$compiler->registerMacroSet(DefaultMacroSet::macroSet($this->container));
			$compiler->registerHelperSet(DefaultMacroSet::helperSet($this->container));

			/** @var $file IFile */
			self::assertInstanceOf(IFile::class, $file = $compiler->template([new File(__DIR__ . '/template/complex/to-be-used.xml')]));
			$template = AbstractHtmlTemplate::template($file, $this->container);
			$template->snippet($this->container->inject($div = new \SomeCoolControl()));
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
	<div data-class="Edde.Common.Html.Tag.ButtonControl" class="button" data-action="http://localhost/foo/bar?a=1&control=SomeCoolControl&action=action-on-the-root"></div>
	<div data-class="Edde.Common.Html.Tag.ButtonControl" class="button" data-action="http://localhost/foo/bar?a=1&control=Edde%5CCommon%5CHtml%5CTag%5CButtonControl&action=%40action-on-the-current-contol"></div>
	<div data-class="Edde.Common.Html.Tag.ButtonControl" class="button just-useless-button-here"></div>
	<div class="edde-placeholder" id="foo"></div>
	<h1>foo1</h1>
	<h2>foo2</h2>
	<h3 class="foo3-with-class">foo3</h3>
	<h4>foo4</h4>
	<h5>foo5</h5>
	<h6>foo6</h6>
</div>
', $div->render());
			$template->snippet($this->container->inject($div = new DivControl()), 'deep-block');
			$div->addClass('root');
			$div->dirty();
			self::assertEquals('<div class="root">
	<div class="really-deep-div-here">
		<div class="deepness-of-a-deep">foo</div>
	</div>
</div>
', $div->render());
		}

		protected function setUp() {
			$this->container = ContainerFactory::create([
				IResourceManager::class => ResourceManager::class,
				IConverterManager::class => ConverterManager::class,
				IXmlParser::class => XmlParser::class,
				IRootDirectory::class => new RootDirectory(__DIR__ . '/temp'),
				IAssetsDirectory::class => new AssetsDirectory(__DIR__ . '/../../../../../src/Edde/assets'),
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
				ICryptEngine::class => CryptEngine::class,
			]);
			/** @var $converterManager IConverterManager */
			$converterManager = $this->container->create(IConverterManager::class);
			$converterManager->registerConverter($this->container->inject(new XmlConverter()));
		}
	}

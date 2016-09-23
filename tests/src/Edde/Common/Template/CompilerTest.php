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
	use Edde\Common\Html\Macro\HtmlMacro;
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

	require_once __DIR__ . '/assets.php';

	/**
	 * Test covering almost all method related to a template compiler.
	 */
	class CompilerTest extends TestCase {
		/**
		 * @var IContainer
		 */
		protected $container;
		protected $flag = false;

		public function call() {
			$this->flag = true;
		}

		public function testComplex() {
			$this->container->inject($compiler = new Compiler(new File(__DIR__ . '/template/complex/layout.xml')));
			$compiler->registerMacroSet(DefaultMacroSet::macroSet($this->container));
			$compiler->registerMacro(new HtmlMacro('custom-control', \AnotherCoolControl::class));
			$compiler->registerHelperSet(DefaultMacroSet::helperSet($this->container));

			/** @var $file IFile */
			self::assertInstanceOf(IFile::class, $file = $compiler->template([new File(__DIR__ . '/template/complex/to-be-used.xml')]));
			$template = AbstractHtmlTemplate::template($file, $this->container);
			$template->snippet($this->container->inject($div = new \SomeCoolControl()));
			self::assertTrue($this->flag, "template didn't called service method call()");
			$div->addClass('root');
			$div->dirty();
			self::assertEquals(file_get_contents(__DIR__ . '/template/complex/result.xml'), $div->render());
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
				'\SomeService\From\Container' => $this,
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

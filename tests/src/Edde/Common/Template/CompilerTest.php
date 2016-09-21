<?php
	declare(strict_types = 1);

	namespace Edde\Common\Template;

	use Edde\Api\Container\IContainer;
	use Edde\Api\Converter\IConverterManager;
	use Edde\Api\Crypt\ICryptEngine;
	use Edde\Api\File\IFile;
	use Edde\Api\File\IRootDirectory;
	use Edde\Api\Resource\IResourceManager;
	use Edde\Api\Xml\IXmlParser;
	use Edde\Common\Converter\ConverterManager;
	use Edde\Common\Crypt\CryptEngine;
	use Edde\Common\File\File;
	use Edde\Common\File\RootDirectory;
	use Edde\Common\Html\Macro\ControlMacro;
	use Edde\Common\Html\Macro\HtmlMacro;
	use Edde\Common\Html\Tag\DivControl;
	use Edde\Common\Resource\ResourceManager;
	use Edde\Common\Template\Inline\BlockInline;
	use Edde\Common\Template\Inline\IncludeInline;
	use Edde\Common\Template\Macro\BlockMacro;
	use Edde\Common\Template\Macro\IncludeMacro;
	use Edde\Common\Template\Macro\UseMacro;
	use Edde\Common\Xml\XmlParser;
	use Edde\Ext\Container\ContainerFactory;
	use Edde\Ext\Converter\XmlConverter;
	use phpunit\framework\TestCase;

	class CompilerTest extends TestCase {
		/**
		 * @var IContainer
		 */
		protected $container;

		public function testComplex() {
			$this->container->inject($compiler = new Compiler(new File(__DIR__ . '/template/complex/layout.xml')));

			$compiler->registerMacro($this->container->inject(new UseMacro()));
			$compiler->registerMacro($this->container->inject(new IncludeMacro()));
			$compiler->registerMacro($this->container->inject(new BlockMacro()));
			$compiler->registerMacro($this->container->inject(new ControlMacro()));
			$compiler->registerMacro($this->container->inject(new HtmlMacro('div', DivControl::class)));

			$compiler->registerInline($this->container->inject(new BlockInline()));
			$compiler->registerInline($this->container->inject(new IncludeInline()));

			/** @var $file IFile */
			self::assertInstanceOf(IFile::class, $file = $compiler->template([new File(__DIR__ . '/template/complex/to-be-used.xml')]));
			(function (IFile $file) {
				require_once($file->getUrl()
					->getAbsoluteUrl());
			})($file);
			$class = str_replace('.php', '', $file->getName());
			$template = $this->container->inject(new $class());
			$template->snippet($this->container->inject($div = new DivControl()));
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
			$template->snippet($this->container->inject($div = new DivControl()), 'deep-block');
			$div->addClass('root');
			$div->dirty();
			self::assertEquals('<div class="root">
	<div class="really-deep-div-here">
		<div class="deepness-of-a-deep"></div>
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
				ICryptEngine::class => CryptEngine::class,
			]);
			/** @var $converterManager IConverterManager */
			$converterManager = $this->container->create(IConverterManager::class);
			$converterManager->registerConverter($this->container->inject(new XmlConverter()));
		}
	}

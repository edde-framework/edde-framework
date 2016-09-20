<?php
	declare(strict_types = 1);

	namespace Edde\Common\Template;

	use Edde\Api\Container\IContainer;
	use Edde\Api\Converter\IConverterManager;
	use Edde\Api\Crypt\ICryptEngine;
	use Edde\Api\File\IFile;
	use Edde\Api\File\IRootDirectory;
	use Edde\Api\Resource\IResourceManager;
	use Edde\Api\Template\ICompiler;
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

		public function testCompile() {
			/** @var $compiler ICompiler */
			$compiler = $this->container->inject(new Compiler(new File(__DIR__ . '/template/complex/layout.xml')));

			$compiler->registerCompileMacro($this->container->inject(new UseMacro()));
			$compiler->registerCompileMacro($this->container->inject(new IncludeMacro()));
			$compiler->registerCompileMacro($this->container->inject(new BlockMacro()));

			$compiler->registerCompileInlineMacro($this->container->inject(new BlockInline()));
			$compiler->registerCompileInlineMacro($this->container->inject(new IncludeInline()));

			$compiler->registerMacro($this->container->inject(new ControlMacro()));
			$compiler->registerMacro($this->container->inject(new HtmlMacro('div', DivControl::class)));

			/** @var $file IFile */
			self::assertInstanceOf(IFile::class, $file = $compiler->template());
			(function (IFile $file) {
				require_once($file->getUrl()
					->getAbsoluteUrl());
			})($file);
			$class = str_replace('.php', '', $file->getName());
			$template = $this->container->inject(new $class());
			$template->snippet();
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

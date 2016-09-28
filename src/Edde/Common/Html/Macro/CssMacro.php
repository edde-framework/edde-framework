<?php
	declare(strict_types = 1);

	namespace Edde\Common\Html\Macro;

	use Edde\Api\File\FileException;
	use Edde\Api\File\IFile;
	use Edde\Api\File\LazyRootDirectoryTrait;
	use Edde\Api\LazyAssetsDirectoryTrait;
	use Edde\Api\Node\INode;
	use Edde\Api\Template\ICompiler;
	use Edde\Api\Template\MacroException;
	use Edde\Common\File\File;

	/**
	 * Css support macro; this will generate item to styleSheetList property in abstract template.
	 */
	class CssMacro extends AbstractHtmlMacro {
		use LazyRootDirectoryTrait;
		use LazyAssetsDirectoryTrait;

		/**
		 * Any sufficiently advanced bug is indistinguishable from a feature.
		 */
		public function __construct() {
			parent::__construct('css');
		}

		/** @noinspection PhpMissingParentCallCommonInspection */
		/**
		 * @inheritdoc
		 * @throws FileException
		 * @throws MacroException
		 */
		public function compile(INode $macro, ICompiler $compiler) {
			$macro->setAttribute('src', $this->file($this->attribute($macro, $compiler, 'src', false), $compiler->getCurrent())
				->getPath());
		}

		/**
		 * resolve css file include
		 *
		 * @param string $src
		 * @param IFile $source
		 *
		 * @return IFile
		 * @throws FileException
		 */
		protected function file(string $src, IFile $source): IFile {
			if (strpos($src, '/') === 0) {
				return $this->rootDirectory->file(substr($src, 1));
			} else if (strpos($src, './') === 0) {
				return $source->getDirectory()
					->file(substr($src, 2));
			} else if (strpos($src, 'edde://') !== false) {
				return $this->assetsDirectory->file(str_replace('edde://', '', $src));
			}
			return new File($src);
		}

		/** @noinspection PhpMissingParentCallCommonInspection */
		/**
		 * @inheritdoc
		 * @throws FileException
		 * @throws MacroException
		 */
		public function macro(INode $macro, ICompiler $compiler) {
			$this->write($compiler, sprintf('$this->styleSheetCompiler->addFile(%s);', var_export($this->attribute($macro, $compiler, 'src', false), true)), 5);
		}
	}

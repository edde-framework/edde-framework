<?php
	declare(strict_types = 1);

	namespace Edde\Common\Html\Macro;

	use Edde\Api\File\IFile;
	use Edde\Api\File\IRootDirectory;
	use Edde\Api\IAssetsDirectory;
	use Edde\Api\Node\INode;
	use Edde\Common\File\File;

	class CssMacro extends AbstractHtmlMacro {
		/**
		 * @var IRootDirectory
		 */
		protected $rootDirectory;

		/**
		 * @var IAssetsDirectory
		 */
		protected $assetsDirectory;

		public function __construct() {
			parent::__construct('css', false);
		}

		public function lazyRootDirectory(IRootDirectory $rootDirectory) {
			$this->rootDirectory = $rootDirectory;
		}

		public function lazyAssetsDirectory(IAssetsDirectory $assetsDirectory) {
			$this->assetsDirectory = $assetsDirectory;
		}

		protected function onMacro() {

			$this->write(sprintf('$this->styleSheetList->addFile(%s);', var_export($this->file($this->attribute('src'), $this->compiler->getSource(), $this->macro)
				->getPath(), true)), 5);
		}

		protected function file(string $src, IFile $source, INode $macro): IFile {
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
	}

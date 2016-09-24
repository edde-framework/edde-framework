<?php
	declare(strict_types = 1);

	namespace Edde\Common\Html\Macro;

	use Edde\Api\File\IFile;
	use Edde\Api\File\IRootDirectory;
	use Edde\Api\IAssetsDirectory;
	use Edde\Api\Node\INode;
	use Edde\Common\File\File;

	/**
	 * JavaScript support.
	 */
	class JsMacro extends AbstractHtmlMacro {
		/**
		 * @var IRootDirectory
		 */
		protected $rootDirectory;
		/**
		 * @var IAssetsDirectory
		 */
		protected $assetsDirectory;

		/**
		 * God called a meeting of George Bush, Vladimir Putin, and Bill Gates and said: "I've given you all the tools you needed to make a better world - you've blown it and I'm ending the world in two weeks."
		 *
		 * George Bush went on TV and said "I have some good news and some bad news. The good news is that God exists. The bad news is that the world will end in two weeks."
		 *
		 * Vladimir Putin called his advisers together and said "I have some bad news and some really bad news. The bad news is that God exists. The really bad news is that the world will end in two weeks."
		 *
		 * Bill Gates called his co-workers together and said "I have some good news and some really great news. The good news is that God thinks I am one of the three most powerful people in the world. The really great news is that we don't have to fix the bugs in Windows Vista."
		 */
		public function __construct() {
			parent::__construct('js', false);
		}

		public function lazyRootDirectory(IRootDirectory $rootDirectory) {
			$this->rootDirectory = $rootDirectory;
		}

		public function lazyAssetsDirectory(IAssetsDirectory $assetsDirectory) {
			$this->assetsDirectory = $assetsDirectory;
		}

		protected function onMacro() {
			$this->write(sprintf('$this->javaScriptList->addFile(%s);', var_export($this->file($this->attribute('src'), $this->compiler->getSource(), $this->macro)
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

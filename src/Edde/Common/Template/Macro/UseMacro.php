<?php
	declare(strict_types = 1);

	namespace Edde\Common\Template\Macro;

	use Edde\Api\File\IFile;
	use Edde\Api\File\IRootDirectory;
	use Edde\Api\Node\INode;
	use Edde\Api\Template\ICompiler;
	use Edde\Api\Template\MacroException;
	use Edde\Common\File\File;
	use Edde\Common\Template\AbstractMacro;

	class UseMacro extends AbstractMacro {
		/**
		 * @var IRootDirectory
		 */
		protected $rootDirectory;

		public function __construct() {
			parent::__construct('t:use');
		}

		public function lazyRootDirectory(IRootDirectory $rootDirectory) {
			$this->rootDirectory = $rootDirectory;
		}

		public function macro(INode $macro, ICompiler $compiler) {
			$compiler->compile(new File($this->getFile($this->attribute($macro, 'src'), $compiler->getCurrent(), $macro)));
		}

		protected function getFile(string $src, IFile $source, INode $macro): string {
			if (strpos($src, '/') === 0) {
				return $this->rootDirectory->filename(substr($src, 1));
			} else if (strpos($src, './') === 0) {
				return $source->getDirectory()
					->filename(substr($src, 2));
			}
			throw new MacroException(sprintf('Unknown "src" attribute value [%s] of macro [%s].', $src, $macro->getPath()));
		}
	}

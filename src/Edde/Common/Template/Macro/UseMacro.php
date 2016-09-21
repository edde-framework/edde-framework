<?php
	declare(strict_types = 1);

	namespace Edde\Common\Template\Macro;

	use Edde\Api\File\IFile;
	use Edde\Api\File\IRootDirectory;
	use Edde\Api\Node\INode;
	use Edde\Api\Template\ICompiler;
	use Edde\Api\Template\MacroException;
	use Edde\Common\Template\AbstractMacro;

	class UseMacro extends AbstractMacro {
		/**
		 * @var IRootDirectory
		 */
		protected $rootDirectory;

		public function __construct() {
			parent::__construct('t:use', true);
		}

		public function lazyRootDirectory(IRootDirectory $rootDirectory) {
			$this->rootDirectory = $rootDirectory;
		}

		public function macro(INode $macro, ICompiler $compiler) {
			$compiler->compile($this->file($this->attribute($macro, 'src'), $compiler->getCurrent(), $macro));
		}

		protected function file(string $src, IFile $source, INode $macro): IFile {
			if (strpos($src, '/') === 0) {
				return $this->rootDirectory->file(substr($src, 1));
			} else if (strpos($src, './') === 0) {
				return $source->getDirectory()
					->file(substr($src, 2));
			}
			throw new MacroException(sprintf('Unknown "src" attribute value [%s] of macro [%s].', $src, $macro->getPath()));
		}
	}

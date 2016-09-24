<?php
	declare(strict_types = 1);

	namespace Edde\Common\Template\Macro;

	use Edde\Api\File\IFile;
	use Edde\Api\File\IRootDirectory;
	use Edde\Api\Node\INode;
	use Edde\Api\Template\MacroException;
	use Edde\Common\Template\AbstractMacro;

	/**
	 * Import macro will load the given template in compile time.
	 */
	class ImportMacro extends AbstractMacro {
		/**
		 * @var IRootDirectory
		 */
		protected $rootDirectory;

		/**
		 * "Real programmers don't comment their code. If it was hard to write, it should be hard to understand."
		 */
		public function __construct() {
			parent::__construct('t:import', true);
		}

		/**
		 * @param IRootDirectory $rootDirectory
		 */
		public function lazyRootDirectory(IRootDirectory $rootDirectory) {
			$this->rootDirectory = $rootDirectory;
		}

		public function onMacro() {
			$this->compiler->compile($this->file($this->attribute('src'), $this->compiler->getCurrent(), $this->macro));
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

<?php
	declare(strict_types = 1);

	namespace Edde\Common\Template\Macro;

	use Edde\Api\File\IFile;
	use Edde\Api\File\IRootDirectory;
	use Edde\Api\Template\ICompiler;
	use Edde\Common\Template\AbstractMacro;

	/**
	 * Compile time include macro.
	 */
	class IncludeMacro extends AbstractMacro {
		/**
		 * @var IRootDirectory
		 */
		protected $rootDirectory;

		/**
		 * If a program is useful, it must be changed.
		 * If a program is useless, it must be documented.
		 */
		public function __construct() {
			parent::__construct('t:include', true);
		}

		/**
		 * @param IRootDirectory $rootDirectory
		 */
		public function lazyRootDirectory(IRootDirectory $rootDirectory) {
			$this->rootDirectory = $rootDirectory;
		}

		public function onMacro() {
			foreach ($this->include($source = $this->attribute('src'), $this->compiler->getCurrent(), $this->compiler) as $node) {
				$node = clone $node;
				/**
				 * mark virtual node root
				 */
				$node->setMeta('root', true);
				$node->setMeta('source', $source);
				$this->macro->addNode($node);
			}
		}

		protected function include (string $src, IFile $source, ICompiler $compiler) {
			if (strpos($src, '/') === 0) {
				return [
					$compiler->compile($this->rootDirectory->file(substr($src, 1))),
				];
			} else if (strpos($src, './') === 0) {
				return [
					$compiler->compile($source->getDirectory()
						->file(substr($src, 2))),
				];
			}
			return $this->compiler->getBlock($src);
		}
	}

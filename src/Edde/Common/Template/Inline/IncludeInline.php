<?php
	declare(strict_types = 1);

	namespace Edde\Common\Template\Inline;

	use Edde\Api\File\IFile;
	use Edde\Api\File\IRootDirectory;
	use Edde\Api\Node\INode;
	use Edde\Api\Template\ICompiler;
	use Edde\Common\Template\AbstractInline;

	/**
	 * Inline support for include macro.
	 */
	class IncludeInline extends AbstractInline {
		/**
		 * @var IRootDirectory
		 */
		protected $rootDirectory;

		/**
		 * MIPS: Meaningless Indicator of Processor Speed.
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
			/** @var $node INode */
			foreach ($this->include($source = $this->attribute(null, false), $this->compiler->getCurrent(), $this->compiler) as $node) {
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

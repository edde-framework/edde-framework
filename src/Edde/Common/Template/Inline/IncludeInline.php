<?php
	declare(strict_types = 1);

	namespace Edde\Common\Template\Inline;

	use Edde\Api\File\IFile;
	use Edde\Api\File\IRootDirectory;
	use Edde\Api\Node\INode;
	use Edde\Api\Template\ICompiler;
	use Edde\Api\Template\MacroException;
	use Edde\Common\Template\AbstractInline;

	/**
	 * Inline support for include macro.
	 */
	class IncludeInline extends AbstractInline {
		/**
		 * @var IRootDirectory
		 */
		protected $rootDirectory;

		public function __construct() {
			parent::__construct('t:include', true);
		}

		public function lazyRootDirectory(IRootDirectory $rootDirectory) {
			$this->rootDirectory = $rootDirectory;
		}

		public function onMacro() {
			foreach ($this->include($this->attribute(null, false), $this->macro, $this->compiler->getCurrent(), $this->compiler) as $node) {
				$this->macro->addNode(clone $node);
			}
		}

		protected function include (string $src, INode $macro, IFile $source, ICompiler $compiler) {
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
			$blockList = $compiler->getVariable('block-list', []);
			if (isset($blockList[$src]) === false) {
				throw new MacroException(sprintf('Unknown include [%s] in macro [%s]; an include may be a file reference of an existing block reference.', $src, $macro->getPath()));
			}
			return $blockList[$src];
		}
	}

<?php
	declare(strict_types = 1);

	namespace Edde\Common\Template\Macro;

	use Edde\Api\File\IFile;
	use Edde\Api\File\IRootDirectory;
	use Edde\Api\Node\INode;
	use Edde\Api\Template\ICompiler;
	use Edde\Api\Template\MacroException;
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
			parent::__construct('include');
		}

		/**
		 * @param IRootDirectory $rootDirectory
		 */
		public function lazyRootDirectory(IRootDirectory $rootDirectory) {
			$this->rootDirectory = $rootDirectory;
		}

		/** @noinspection PhpMissingParentCallCommonInspection */
		/**
		 * @inheritdoc
		 * @throws MacroException
		 */
		public function compileInline(INode $macro, ICompiler $compiler) {
			/** @var $node INode */
			foreach ($this->include($source = $this->extract($macro, 't:' . $this->getName()), $compiler->getCurrent(), $compiler) as $node) {
				$node = clone $node;
				/**
				 * mark virtual node root
				 */
				$node->setMeta('root', true);
				$node->setMeta('source', $source);
				$macro->addNode($node);
			}
		}

		/** @noinspection PhpMissingParentCallCommonInspection */

		/**
		 * compute and execute include from the given source
		 *
		 * @param string $src
		 * @param IFile $source
		 * @param ICompiler $compiler
		 *
		 * @return array
		 */
		protected function include (string $src, IFile $source, ICompiler $compiler) {
			if (strpos($src, '/') === 0) {
				return [
					$compiler->file($this->rootDirectory->file(substr($src, 1))),
				];
			} else if (strpos($src, './') === 0) {
				return [
					$compiler->file($source->getDirectory()
						->file(substr($src, 2))),
				];
			}
			return $compiler->getBlock($src);
		}

		/** @noinspection PhpMissingParentCallCommonInspection */
		/**
		 * @inheritdoc
		 * @throws MacroException
		 */
		public function compile(INode $macro, ICompiler $compiler) {
			foreach ($this->include($source = $this->attribute($macro, $compiler, 'src'), $compiler->getCurrent(), $compiler) as $node) {
				$node = clone $node;
				/**
				 * mark virtual node root
				 */
				$node->setMeta('root', true);
				$node->setMeta('source', $source);
				$macro->addNode($node);
			}
		}
	}

<?php
	declare(strict_types = 1);

	namespace Edde\Common\Html\Macro;

	use Edde\Api\Node\INode;
	use Edde\Api\Template\ICompiler;
	use Edde\Common\File\HomeDirectoryTrait;
	use Edde\Common\Template\AbstractMacro;
	use Edde\Common\Usable\UsableTrait;

	/**
	 * Root control macro for template generation.
	 */
	class ControlMacro extends AbstractMacro {
		use UsableTrait;
		use HomeDirectoryTrait;

		public function __construct() {
			parent::__construct('control');
		}

		public function macro(INode $macro, ICompiler $compiler) {
			$this->use();
			$file = $this->homeDirectory->file(sha1($compiler->getSource()
					->getPath() . '.php'));
			$file->write("foo");
			foreach ($macro->getNodeList() as $node) {
				$compiler->macro($node);
			}
			return true;
		}

		protected function prepare() {
			$this->home('.template');
		}
	}

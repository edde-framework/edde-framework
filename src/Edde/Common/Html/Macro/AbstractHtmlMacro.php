<?php
	declare(strict_types = 1);

	namespace Edde\Common\Html\Macro;

	use Edde\Api\File\IFile;
	use Edde\Api\Node\INode;
	use Edde\Api\Template\ICompiler;
	use Edde\Common\Template\AbstractMacro;

	abstract class AbstractHtmlMacro extends AbstractMacro {
		/**
		 * @var INode
		 */
		protected $macro;
		/**
		 * @var ICompiler
		 */
		protected $compiler;

		public function macro(INode $macro, ICompiler $compiler) {
			$this->macro = $macro;
			$this->compiler = $compiler;
			return $this->onMacro($macro);
		}

		abstract protected function onMacro(INode $macro);

		protected function write(string $write, int $indents = null) {
			/** @var $file IFile */
			$file = $this->compiler->getValue('file');
			$file->write(($indents ? str_repeat("\t", $indents) : '') . $write . "\n");
		}

		protected function compile() {
			foreach ($this->macro->getNodeList() as $node) {
				$this->compiler->macro($node);
			}
		}
	}

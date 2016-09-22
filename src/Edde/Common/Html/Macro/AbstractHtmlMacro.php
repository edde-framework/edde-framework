<?php
	declare(strict_types = 1);

	namespace Edde\Common\Html\Macro;

	use Edde\Api\File\IFile;
	use Edde\Common\Template\AbstractMacro;

	abstract class AbstractHtmlMacro extends AbstractMacro {
		protected function write(string $write, int $indents = null) {
			/** @var $file IFile */
			$file = $this->compiler->getVariable('file');
			$file->write(($indents ? str_repeat("\t", $indents) : '') . $write . "\n");
		}

		protected function compile() {
			foreach ($this->macro->getNodeList() as $node) {
				$this->compiler->runtimeMacro($node);
			}
		}
	}

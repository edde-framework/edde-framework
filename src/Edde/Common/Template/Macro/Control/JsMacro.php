<?php
	declare(strict_types = 1);

	namespace Edde\Common\Template\Macro\Control;

	use Edde\Api\Node\INode;
	use Edde\Api\Template\ICompiler;
	use Edde\Common\Template\AbstractMacro;

	class JsMacro extends AbstractMacro {
		public function __construct() {
			parent::__construct(['js']);
		}

		public function run(INode $root, ICompiler $compiler) {
			$destination = $compiler->getDestination();
			$destination->write(sprintf("\t\t\t\$this->javaScriptCompiler->addFile('%s');\n", $compiler->file($root->getAttribute('src'))));
		}
	}

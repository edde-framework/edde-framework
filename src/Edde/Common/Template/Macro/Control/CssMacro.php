<?php
	declare(strict_types = 1);

	namespace Edde\Common\Template\Macro\Control;

	use Edde\Api\Node\INode;
	use Edde\Api\Template\ICompiler;
	use Edde\Common\Template\AbstractMacro;

	class CssMacro extends AbstractMacro {
		public function __construct() {
			parent::__construct(['css']);
		}

		public function run(INode $root, ICompiler $compiler) {
			$destination = $compiler->getDestination();
			$destination->write(sprintf("\t\t\t\$this->styleSheetCompiler->addFile('%s');\n", $compiler->file($root->getAttribute('src'))));
		}
	}

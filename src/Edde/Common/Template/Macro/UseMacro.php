<?php
	declare(strict_types = 1);

	namespace Edde\Common\Template\Macro;

	use Edde\Api\File\IFile;
	use Edde\Api\Node\INode;
	use Edde\Api\Template\ICompiler;
	use Edde\Common\Template\AbstractMacro;

	class UseMacro extends AbstractMacro {
		public function __construct() {
			parent::__construct('t:use');
		}

		public function macro(INode $node, IFile $source, ICompiler $compiler) {
//			$compiler->compile(new File());
		}
	}

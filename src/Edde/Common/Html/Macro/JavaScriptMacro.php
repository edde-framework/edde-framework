<?php
	declare(strict_types = 1);

	namespace Edde\Common\Html\Macro;

	use Edde\Api\Control\IControl;
	use Edde\Api\Node\INode;
	use Edde\Api\Template\ICompiler;
	use Edde\Common\Template\AbstractMacro;

	class JavaScriptMacro extends AbstractMacro {
		public function __construct() {
			parent::__construct([
				'js',
			]);
		}

		public function macro(INode $macro, INode $element, ICompiler $compiler) {
			$this->checkLeaf($macro, $element);
			$destination = $compiler->getDestination();
			switch ($macro->getName()) {
				case 'js':
					$this->checkAttribute($macro, $element, 'src');
					$destination->write(sprintf("\t\t\t\$controlList[null][] = function(%s \$root) use(&\$controlList) {\n", IControl::class));
					$destination->write(sprintf("\t\t\t\t\$this->javaScriptCompiler->addFile(%s);\n", $compiler->delimite($macro->getAttribute('src'))));
					$destination->write("\t\t\t};\n");
					break;
			}
		}
	}

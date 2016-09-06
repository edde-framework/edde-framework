<?php
	declare(strict_types = 1);

	namespace Edde\Common\Html\Macro;

	use Edde\Api\Control\IControl;
	use Edde\Api\Node\INode;
	use Edde\Api\Template\ICompiler;
	use Edde\Common\Template\AbstractMacro;

	class StyleSheetMacro extends AbstractMacro {
		public function __construct() {
			parent::__construct([
				'css',
			]);
		}

		public function macro(INode $macro, INode $element, ICompiler $compiler) {
			$this->checkLeaf($macro, $element);
			$destination = $compiler->getDestination();
			switch ($macro->getName()) {
				case 'css':
					$this->checkAttribute($macro, $element, 'src');
					$destination->write(sprintf("\t\t\t\$controlList[null][] = function(%s \$root) use(&\$controlList, &\$stash) {\n", IControl::class));
					$destination->write(sprintf("\t\t\t\t\$this->styleSheetCompiler->addFile(%s);\n", $compiler->delimite($macro->getAttribute('src'))));
					$destination->write("\t\t\t};\n");
					break;
			}
		}
	}

<?php
	declare(strict_types = 1);

	namespace Edde\Common\Html\Macro;

	use Edde\Api\Node\INode;
	use Edde\Api\Template\ICompiler;

	class StyleSheetMacro extends AbstractHtmlMacro {
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
					$this->start($macro, $element, $compiler);
					$destination->write(sprintf("\t\t\t\t\$this->styleSheetCompiler->addFile(%s);\n", $compiler->delimite($macro->getAttribute('src'))));
					$this->end($macro, $element, $compiler);
					break;
			}
		}
	}

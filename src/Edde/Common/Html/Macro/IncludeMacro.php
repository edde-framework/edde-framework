<?php
	declare(strict_types = 1);

	namespace Edde\Common\Html\Macro;

	use Edde\Api\Node\INode;
	use Edde\Api\Template\ICompiler;

	class IncludeMacro extends AbstractHtmlMacro {
		public function __construct() {
			parent::__construct([
				'include',
				'define',
				'block',
			]);
		}

		public function macro(INode $macro, INode $element, ICompiler $compiler) {
			$destination = $compiler->getDestination();
			switch ($macro->getName()) {
				case 'include':
					$src = null;
					if ($macro->getMeta('inline')) {
						$this->checkValue($macro, $element);
						$src = $macro->getValue();
					} else {
						$this->checkAttribute($macro, $element, 'src');
						$src = $macro->getAttribute('src');
					}
					$this->start($macro, $element, $compiler);
					$destination->write(sprintf("\t\t\t\t\$controlList[%s](\$control);\n", $compiler->delimite($src)));
					$this->dependencies($macro, $compiler);
					$this->end($macro, $element, $compiler);
					break;
				case 'define':
					$this->checkValue($macro, $element);
					$this->start($macro, $element, $compiler);
					$destination->write(sprintf("\t\t\t\t\$controlList[%s](\$control);\n", $compiler->delimite($id = $macro->getValue())));
					$this->end($macro, $element, $compiler, false);
					$macro->setMeta('control', $id);
					$this->lambda($macro, $element, $compiler);
					break;
				case 'block':
					$this->checkAttribute($macro, $element, 'name');
					$this->start($macro, $element, $compiler);
					$destination->write(sprintf("\t\t\t\t\$controlList[%s](\$control);\n", $compiler->delimite($id = $macro->getAttribute('name'))));
					$this->end($macro, $element, $compiler, false);
					$macro->setMeta('control', $id);
					$this->lambda($macro, $element, $compiler);
					break;
			}
		}
	}

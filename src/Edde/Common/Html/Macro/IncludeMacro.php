<?php
	declare(strict_types = 1);

	namespace Edde\Common\Html\Macro;

	use Edde\Api\Node\INode;
	use Edde\Api\Template\ICompiler;

	class IncludeMacro extends AbstractHtmlMacro {
		public function __construct() {
			parent::__construct([
				'include',
				'block',
			]);
		}

		public function macro(INode $macro, INode $element, ICompiler $compiler) {
			$destination = $compiler->getDestination();
			switch ($macro->getName()) {
				case 'include':
					$macro->getMeta('inline') ? $this->checkValue($macro, $element) : $this->checkAttribute($macro, $element, 'src');
					$this->start($macro, $element, $compiler);
					$destination->write(sprintf("\t\t\t\t\$this->controlList[%s](\$control);\n", $compiler->delimite($macro->getAttribute('src', $macro->getValue()))));
					$this->dependencies($macro, $compiler);
					$this->end($macro, $element, $compiler);
					break;
				case 'block':
					$macro->getMeta('inline') ? $this->checkValue($macro, $element) : $this->checkAttribute($macro, $element, 'name');
					$this->start($macro, $element, $compiler);
					$destination->write(sprintf("\t\t\t\t\$this->controlList[%s](\$control);\n", $compiler->delimite($id = $macro->getAttribute('name', $macro->getValue()))));
					$this->end($macro, $element, $compiler, false);
					$macro->setMeta('control', $id);
					$this->lambda($macro, $element, $compiler);
					break;
			}
		}
	}

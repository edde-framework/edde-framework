<?php
	declare(strict_types = 1);

	namespace Edde\Common\Html\Macro;

	use Edde\Api\Node\INode;
	use Edde\Api\Template\ICompiler;

	class SnippetMacro extends AbstractHtmlMacro {
		public function __construct() {
			parent::__construct([
				'snippet',
			]);
		}

		public function macro(INode $macro, INode $element, ICompiler $compiler) {
			$destination = $compiler->getDestination();
			switch ($macro->getName()) {
				case 'snippet':
					$macro->getMeta('inline') ? $this->checkValue($macro, $element) : $this->checkAttribute($macro, $element, 'name');
					$name = $macro->getAttribute('name', $macro->getValue());
					$this->start($macro, $element, $compiler);
					$id = $compiler->delimite($name);
					$destination->write(sprintf("\t\t\t\t\$this->root->addSnippet(%s, \$this->controlList[%s]);\n", $id, $id));
					$this->end($macro, $element, $compiler, false);
					$macro->setMeta('control', $name);
					$this->lambda($macro, $element, $compiler);
					break;
			}
		}
	}

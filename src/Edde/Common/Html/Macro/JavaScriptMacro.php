<?php
	declare(strict_types = 1);

	namespace Edde\Common\Html\Macro;

	use Edde\Api\Node\INode;
	use Edde\Api\Template\ICompiler;

	class JavaScriptMacro extends AbstractHtmlMacro {
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
					$this->start($macro, $element, $compiler);
					$destination->write(sprintf("\t\t\t\t\$this->javaScriptCompiler->addFile(%s);\n", $compiler->delimite($macro->getAttribute('src'))));
					$this->end($macro, $element, $compiler);
					break;
			}
		}
	}

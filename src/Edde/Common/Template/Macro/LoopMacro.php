<?php
	declare(strict_types = 1);

	namespace Edde\Common\Template\Macro;

	use Edde\Api\Node\INode;
	use Edde\Api\Template\ICompiler;
	use Edde\Common\Template\AbstractMacro;

	class LoopMacro extends AbstractMacro {
		public function __construct() {
			parent::__construct([
				'loop',
				'm:loop',
			]);
		}

		public function run(INode $root, ICompiler $compiler) {
			$destination = $compiler->getDestination();
			switch ($root->getName()) {
				case 'loop':
					$destination->write(sprintf("\t\t\tforeach(%s as \$key => \$item) {\n", $compiler->value($root->getAttribute('src'))));
					$this->macro($root, $compiler);
					$destination->write("\t\t\t}\n");
					break;
				case 'm:loop':
					$destination->write(sprintf("\t\t\tforeach(%s as \$key => \$item) {\n", $compiler->value($root->getValue())));
					$this->macro($root, $compiler);
					$destination->write("\t\t\t}\n");
					break;
			}
		}
	}

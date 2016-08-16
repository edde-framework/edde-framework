<?php
	declare(strict_types = 1);

	namespace Edde\Common\Template\Macro;

	use Edde\Api\Node\INode;
	use Edde\Api\Template\ICompiler;
	use Edde\Common\Template\AbstractMacro;

	class LoopMacro extends AbstractMacro {
		/**
		 * @var \SplStack
		 */
		protected $variableStack;

		public function __construct() {
			parent::__construct([
				'loop',
				'm:loop',
			]);
			$this->variableStack = new \SplStack();
		}

		public function run(INode $root, ICompiler $compiler) {
			$destination = $compiler->getDestination();
			switch ($root->getName()) {
				case 'loop':
					$this->variableStack->push([

					]);
					$destination->write(sprintf("\t\t\tforeach(%s as $%s => $%s) {\n", $compiler->value($root->getAttribute('src')), 'a', 'b'));
					$this->macro($root, $compiler);
					$destination->write("\t\t\t}\n");
					break;
				case 'm:loop':
					break;
			}
		}
	}

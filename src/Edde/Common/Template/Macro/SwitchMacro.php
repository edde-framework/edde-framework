<?php
	declare(strict_types = 1);

	namespace Edde\Common\Template\Macro;

	use Edde\Api\Crypt\ICryptEngine;
	use Edde\Api\Node\INode;
	use Edde\Api\Template\ICompiler;
	use Edde\Common\Strings\StringUtils;
	use Edde\Common\Template\AbstractMacro;

	class SwitchMacro extends AbstractMacro {
		/**
		 * @var ICryptEngine
		 */
		protected $cryptEngine;
		/**
		 * @var \SplStack
		 */
		protected $stack;

		public function __construct(ICryptEngine $cryptEngine) {
			parent::__construct([
				'm:switch',
				'm:case',
			]);
			$this->cryptEngine = $cryptEngine;
			$this->stack = new \SplStack();
		}

		public function run(INode $root, ICompiler $compiler) {
			$destination = $compiler->getDestination();
			switch ($root->getName()) {
				case 'm:switch':
					$this->stack->push($id = StringUtils::camelize($this->cryptEngine->guid()));
					$destination->write(sprintf("\t\t\t\$_%s = %s;\n", $id, $compiler->value($root->getValue())));
					$this->macro($root, $compiler);
					break;
				case 'm:case':
					$destination->write(sprintf("\t\t\tif(\$_%s === %s) {\n", $this->stack->top(), $compiler->value($root->getValue())));
					$this->macro($root, $compiler);
					$destination->write("\t\t\t}\n");
					break;
			}
		}

		public function __clone() {
			$this->stack = new \SplStack();
		}
	}
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
		protected $loopStack;

		public function __construct() {
			parent::__construct([
				'loop',
				'm:loop',
			]);
			$this->loopStack = new \SplStack();
		}

		public function variable(string $string) {
			switch ($string) {
				case ':$':
					$loop = $this->loopStack->top();
					return '$' . $loop[1];
				case ':#':
					$loop = $this->loopStack->top();
					return '$' . $loop[0];
			}
			return null;
		}

		public function run(INode $root, ICompiler $compiler) {
			$destination = $compiler->getDestination();
			$this->loopStack->push($loop = [
				'key_' . sha1(random_bytes(64)),
				'value_' . sha1(random_bytes(64)),
			]);
			switch ($root->getName()) {
				case 'loop':
					$destination->write(sprintf("\t\t\tforeach(%s as \$%s => \$%s) {\n", $compiler->value($root->getAttribute('src')), $loop[0], $loop[1]));
					$this->macro($root, $compiler);
					$this->loopStack->pop();
					$destination->write("\t\t\t}\n");
					break;
				case 'm:loop':
					$destination->write(sprintf("\t\t\tforeach(%s as $%s => $%s) {\n", $compiler->value($root->getValue()), $loop[0], $loop[1]));
					$this->macro($root, $compiler);
					$this->loopStack->pop();
					$destination->write("\t\t\t}\n");
					break;
			}
		}
	}

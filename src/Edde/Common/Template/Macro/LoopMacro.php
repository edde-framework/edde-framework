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

		public function variable(string $string, ICompiler $compiler) {
			switch ($string) {
				case ':$':
					list($key, $value) = $this->loopStack->top();
					return '$' . $value;
				case ':#':
					list($key, $value) = $this->loopStack->top();
					return '$' . $key;
			}
			if (strpos($string, ':$') !== false) {
				list($key, $value) = $this->loopStack->top();
				return '$' . $value . $compiler->delimite(str_replace(':$', '->', $string));
			}
			return null;
		}

		public function macro(INode $macro, INode $element, ICompiler $compiler) {
			$destination = $compiler->getDestination();
			$this->loopStack->push(list($key, $value) = [
				'key_' . sha1(random_bytes(64)),
				'value_' . sha1(random_bytes(64)),
			]);
			switch ($macro->getName()) {
				case 'loop':
					$this->checkAttribute($macro, $element, 'src');
					$destination->write(sprintf("\t\t\tforeach(%s as \$%s => \$%s) {\n", $compiler->delimite($element->getAttribute('src')), $key, $value));
					$this->element($element, $compiler);
					$destination->write("\t\t\t}\n");
					$this->loopStack->pop();
					break;
				case 'm:loop':
					$this->checkValue($macro, $element);
					$destination->write(sprintf("\t\t\tforeach(%s as $%s => $%s) {\n", $compiler->delimite($macro->getValue()), $key, $value));
					$compiler->macro($element, $element);
					$destination->write("\t\t\t}\n");
					$this->loopStack->pop();
					break;
			}
		}

		public function __clone() {
			$this->loopStack = new \SplStack();
		}
	}

<?php
	declare(strict_types = 1);

	namespace Edde\Common\Html\Macro;

	use Edde\Api\Node\INode;
	use Edde\Api\Template\ICompiler;

	class LoopMacro extends AbstractHtmlMacro {
		/**
		 * @var \SplStack
		 */
		protected $loopStack;

		public function __construct() {
			parent::__construct([
				'loop',
			]);
			$this->loopStack = new \SplStack();
		}

		public function variable(string $string, ICompiler $compiler) {
			switch ($string) {
				case ':$':
					list($key, $value) = $this->loopStack->top();
					return '$this->stash[' . $compiler->delimite($value) . ']';
				case ':#':
					list($key, $value) = $this->loopStack->top();
					return '$this->stash[' . $compiler->delimite($key) . ']';
			}
			if (strpos($string, ':$') !== false) {
				list($key, $value) = $this->loopStack->top();
				return '$this->stash[' . $compiler->delimite($value) . ']' . $compiler->delimite(str_replace(':$', '->', $string));
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
					$source = $macro->getAttribute('src', $macro->getValue());
					$this->start($macro, $element, $compiler);
					$destination->write(sprintf("\t\t\t\tforeach(%s as \$%s => \$%s) {\n", $compiler->delimite($source), $key, $value));
					$destination->write(sprintf("\t\t\t\t\t\$this->stash[%s] = \$%s;\n", $compiler->delimite($key), $key));
					$destination->write(sprintf("\t\t\t\t\t\$this->stash[%s] = \$%s;\n", $compiler->delimite($value), $value));
					$destination->write(sprintf("\t\t\t/** %s (%s) */\n", $macro->getPath(), $element->getPath()));
					$this->dependencies($macro, $compiler);
					$destination->write("\t\t\t\t}\n");
					$this->end($macro, $element, $compiler);
					$this->loopStack->pop();
					break;
			}
		}

		public function __clone() {
			$this->loopStack = new \SplStack();
		}
	}

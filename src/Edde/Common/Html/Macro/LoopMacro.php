<?php
	declare(strict_types = 1);

	namespace Edde\Common\Html\Macro;

	use Edde\Api\Control\IControl;
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
					return '$stash[' . $compiler->delimite($value) . ']';
				case ':#':
					list($key, $value) = $this->loopStack->top();
					return '$stash[' . $compiler->delimite($key) . ']';
			}
			if (strpos($string, ':$') !== false) {
				list($key, $value) = $this->loopStack->top();
				return '$stash[' . $compiler->delimite($value) . ']' . $compiler->delimite(str_replace(':$', '->', $string));
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
					$destination->write(sprintf("\t\t\t\t/** macro: %s */\n", $macro->getPath()));
					$destination->write(sprintf("\t\t\t\$controlList[%s] = function(%s \$root) use(&\$controlList, &\$stash) {\n", $compiler->delimite($macro->getMeta('control')), IControl::class));
					$destination->write("\t\t\t\t\$control = \$root;\n");
					$destination->write(sprintf("\t\t\t\tforeach(%s as \$%s => \$%s) {\n", $compiler->delimite($source), $key, $value));
					$destination->write(sprintf("\t\t\t\t\t\$stash[%s] = \$%s;\n", $compiler->delimite($key), $key));
					$destination->write(sprintf("\t\t\t\t\t\$stash[%s] = \$%s;\n", $compiler->delimite($value), $value));
					$destination->write(sprintf("\t\t\t/** %s (%s) */\n", $macro->getPath(), $element->getPath()));
					foreach ($macro->getNodeList() as $node) {
						$destination->write(sprintf("\t\t\t\t/** %s */\n", $node->getPath()));
						$destination->write(sprintf("\t\t\t\t\$controlList[%s](\$control);\n", $compiler->delimite($node->getMeta('control'))));
					}
					$destination->write("\t\t\t\t}\n");
					$destination->write("\t\t\t};\n");
					$this->element($macro, $compiler);
					$this->loopStack->pop();
					break;
			}
		}

		public function __clone() {
			$this->loopStack = new \SplStack();
		}
	}

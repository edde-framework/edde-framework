<?php
	declare(strict_types = 1);

	namespace Edde\Common\Html\Macro;

	use Edde\Api\Control\IControl;
	use Edde\Api\Crypt\ICryptEngine;
	use Edde\Api\Node\INode;
	use Edde\Api\Template\ICompiler;
	use Edde\Common\Container\LazyInjectTrait;
	use Edde\Common\Strings\StringUtils;
	use Edde\Common\Template\AbstractMacro;

	class SwitchMacro extends AbstractMacro {
		use LazyInjectTrait;
		/**
		 * @var \SplStack
		 */
		protected $stack;
		/**
		 * @var ICryptEngine
		 */
		protected $cryptEngine;

		public function __construct() {
			parent::__construct([
				'switch',
				'case',
			]);
			$this->stack = new \SplStack();
		}

		public function lazyCryptEngine(ICryptEngine $cryptEngine) {
			$this->cryptEngine = $cryptEngine;
		}

		public function macro(INode $macro, INode $element, ICompiler $compiler) {
			$destination = $compiler->getDestination();
			switch ($macro->getName()) {
				case 'switch':
					$this->checkValue($macro, $element);
					$this->stack->push($id = StringUtils::camelize($this->cryptEngine->guid()));
					$destination->write(sprintf("\t\t\t/** %s */\n", $macro->getPath()));
					$destination->write(sprintf("\t\t\t\$controlList[%s] = function(%s \$root) use(&\$controlList, &\$stash) {\n", $compiler->delimite($element->getMeta('control')), IControl::class));
					$destination->write(sprintf("\t\t\t\$stash[%s] = %s;\n", $compiler->delimite($id), $compiler->delimite($macro->getValue())));
					$destination->write("\t\t\t\t\$control = \$root;\n");
					foreach ($element->getNodeList() as $node) {
						$destination->write(sprintf("\t\t\t\t\$controlList[%s](\$control);\n", $compiler->delimite($node->getMeta('control'))));
					}
					$destination->write("\t\t\t};\n");
					$this->element($element, $compiler);
					break;
				case 'case':
					$this->checkValue($macro, $element);
					$destination->write(sprintf("\t\t\t/** %s */\n", $macro->getPath()));
					$destination->write(sprintf("\t\t\t\$controlList[%s] = function(%s \$root) use(&\$controlList, &\$stash) {\n", $compiler->delimite($macro->getMeta('control')), IControl::class));
					$destination->write(sprintf("\t\t\tif(\$stash[%s] === %s) {\n", $compiler->delimite($this->stack->top()), $compiler->delimite($macro->getValue())));
					$destination->write(sprintf("\t\t\t/** %s */\n", $element->getPath()));
					$destination->write(sprintf("\t\t\t\$controlList[%s] = function(%s \$root) use(&\$controlList, &\$stash) {\n", $compiler->delimite($element->getMeta('control')), IControl::class));
					$destination->write("\t\t\t\t\$control = \$root;\n");
					foreach ($element->getNodeList() as $node) {
						$destination->write(sprintf("\t\t\t\t\$controlList[%s](\$control);\n", $compiler->delimite($node->getMeta('control'))));
					}
					$destination->write("\t\t\t};\n");
					$this->element($element, $compiler);
					$destination->write("\t\t\t}\n");
					$destination->write("\t\t\t};\n");
					break;
			}
		}

		public function __clone() {
			$this->stack = new \SplStack();
		}
	}

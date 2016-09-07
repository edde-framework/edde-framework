<?php
	declare(strict_types = 1);

	namespace Edde\Common\Html\Macro;

	use Edde\Api\Crypt\ICryptEngine;
	use Edde\Api\Node\INode;
	use Edde\Api\Template\ICompiler;
	use Edde\Common\Container\LazyInjectTrait;
	use Edde\Common\Strings\StringUtils;

	class SwitchMacro extends AbstractHtmlMacro {
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
					$this->start($macro, $element, $compiler);
					$destination->write(sprintf("\t\t\t\$stash[%s] = %s;\n", $compiler->delimite($id), $compiler->delimite($macro->getValue())));
					$this->dependencies($macro, $compiler);
					$this->end($macro, $element, $compiler);
					break;
				case 'case':
					$this->checkValue($macro, $element);
					$this->start($macro, $element, $compiler);
					$destination->write(sprintf("\t\t\tif(\$stash[%s] === %s) {\n", $compiler->delimite($this->stack->top()), $compiler->delimite($macro->getAttribute('case', $macro->getValue()))));
					$this->dependencies($macro, $compiler);
					$destination->write("\t\t\t}\n");
					$this->end($macro, $element, $compiler);
					break;
			}
		}

		public function __clone() {
			$this->stack = new \SplStack();
		}
	}

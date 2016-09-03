<?php
	declare(strict_types = 1);

	namespace Edde\Common\Template\Macro;

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
				'm:switch',
				'm:case',
				'case',
			]);
			$this->stack = new \SplStack();
		}

		public function lazyCryptEngine(ICryptEngine $cryptEngine) {
			$this->cryptEngine = $cryptEngine;
		}

		public function run(INode $root, ICompiler $compiler, callable $callback = null) {
			$destination = $compiler->getDestination();
			switch ($root->getName()) {
				case 'm:switch':
					$this->stack->push($id = StringUtils::camelize($this->cryptEngine->guid()));
					$destination->write(sprintf("\t\t\t\$_%s = %s;\n", $id, $compiler->value($root->getValue())));
					$this->macro($root, $compiler, $callback);
					break;
				/** @noinspection PhpMissingBreakStatementInspection */
				case 'case':
					$root->setValue($root->getAttribute('case'));
				case 'm:case':
					$destination->write(sprintf("\t\t\tif(\$_%s === %s) {\n", $this->stack->top(), $compiler->value($root->getValue())));
					$this->macro($root, $compiler, $callback);
					$destination->write("\t\t\t}\n");
					break;
			}
		}

		public function __clone() {
			$this->stack = new \SplStack();
		}
	}

<?php
	declare(strict_types = 1);

	namespace Edde\Common\Template\Macro\Control;

	use Edde\Api\File\IFile;
	use Edde\Api\Node\INode;
	use Edde\Api\Template\ICompiler;
	use Edde\Common\Template\AbstractMacro;

	abstract class AbstractControlMacro extends AbstractMacro {
		/**
		 * @var string
		 */
		protected $control;

		/**
		 * @param array $macroList
		 * @param $control
		 */
		public function __construct(array $macroList, string $control) {
			parent::__construct($macroList);
			$this->control = $control;
		}

		public function run(INode $root, ICompiler $compiler, callable $callback = null) {
			$destination = $compiler->getDestination();
			$destination->write("\t\t\t\$parent = \$this->stack->top();\n");
			$destination->write(sprintf("\t\t\t\$parent->addControl(\$control = \$this->container->create('%s'));\n", $this->control));
			$this->writeTextValue($root, $destination, $compiler);
			$attributeList = $this->getAttributeList($root, $compiler);
			unset($attributeList['value']);
			$this->writeAttributeList($attributeList, $destination);
			$this->macro($root, $compiler, $callback);
		}

		protected function writeTextValue(INode $root, IFile $destination, ICompiler $compiler) {
			if ($root->isLeaf() && ($text = $root->getValue($root->getAttribute('value'))) !== null) {
				$destination->write(sprintf("\t\t\t\$control->setText(%s);\n", $compiler->value($text)));
			}
		}

		protected function writeAttributeList(array $attributeList, IFile $destination) {
			if ($attributeList !== []) {
				$export = [];
				foreach ($attributeList as $name => $value) {
					$export[] = "'" . $name . "' => " . $value;
				}
				$destination->write(sprintf("\t\t\t\$control->setAttributeList([%s]);\n", implode(",\n", $export)));
			}
		}

		protected function macro(INode $root, ICompiler $compiler, callable $callback = null) {
			$destination = $compiler->getDestination();
			if ($root->isLeaf()) {
				parent::macro($root, $compiler, $callback);
				if ($callback) {
					$callback($compiler);
				}
				return;
			}
			$destination->write("\t\t\t\$this->stack->push(\$control);\n");
			parent::macro($root, $compiler, $callback);
			$destination->write("\t\t\t\$control = \$this->stack->pop();\n");
		}
	}

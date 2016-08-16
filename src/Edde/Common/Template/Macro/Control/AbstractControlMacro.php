<?php
	declare(strict_types = 1);

	namespace Edde\Common\Template\Macro\Control;

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

		public function run(INode $root, ICompiler $compiler) {
			$file = $compiler->getDestination();
			$file->write("\t\t\t\$parent = \$this->stack->top();\n");
			$file->write(sprintf("\t\t\t\$parent->addControl(\$control = \$this->container->create('%s'));\n", $this->control));
			if ($root->isLeaf() && ($text = $root->getValue($root->getAttribute('value'))) !== null) {
				$file->write(sprintf("\t\t\t\$control->setText('%s');\n", $text));
			}
			$attributeList = $this->getAttributeList($root);
			unset($attributeList['value']);
			if ($attributeList !== []) {
				$file->write(sprintf("\t\t\t\$control->setAttributeList(%s);\n", var_export($attributeList, true)));
			}
			$this->macro($root, $compiler);
		}

		protected function getAttributeList(INode $node) {
			return $node->getAttributeList();
		}

		protected function macro(INode $root, ICompiler $compiler) {
			$destination = $compiler->getDestination();
			if ($root->isLeaf()) {
				parent::macro($root, $compiler);
				return;
			}
			$destination->write("\t\t\t\$this->stack->push(\$control);\n");
			parent::macro($root, $compiler);
			$destination->write("\t\t\t\$this->stack->pop();\n");
		}
	}

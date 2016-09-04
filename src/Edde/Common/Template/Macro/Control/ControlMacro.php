<?php
	declare(strict_types = 1);

	namespace Edde\Common\Template\Macro\Control;

	use Edde\Api\File\IFile;
	use Edde\Api\Node\INode;
	use Edde\Api\Template\ICompiler;
	use Edde\Common\Template\AbstractMacro;

	class ControlMacro extends AbstractMacro {
		/**
		 * @var string
		 */
		protected $control;

		/**
		 * @param array $macroList
		 * @param $control
		 */
		public function __construct($macroList, string $control) {
			parent::__construct((array)$macroList);
			$this->control = $control;
		}

		public function macro(INode $macro, INode $element, ICompiler $compiler) {
			$destination = $compiler->getDestination();
			$destination->write("\t\t\t\$parent = \$this->stack->top();\n");
			$destination->write(sprintf("\t\t\t\$parent->addControl(\$control = \$this->container->create('%s'));\n", $this->control));
			$this->writeTextValue($element, $destination, $compiler);
			$attributeList = $this->getAttributeList($element, $compiler);
			unset($attributeList['value']);
			$this->writeAttributeList($attributeList, $destination);
			$this->element($element, $compiler);
		}

		protected function writeTextValue(INode $root, IFile $destination, ICompiler $compiler) {
			if ($root->isLeaf() && ($text = $root->getValue($root->getAttribute('value'))) !== null) {
				$destination->write(sprintf("\t\t\t\$control->setText(%s);\n", $compiler->delimite($text)));
			}
		}

		protected function writeAttributeList(array $attributeList, IFile $destination) {
			if (empty($attributeList) === false) {
				$export = [];
				foreach ($attributeList as $name => $value) {
					$export[] = "'" . $name . "' => " . $value;
				}
				$destination->write(sprintf("\t\t\t\$control->setAttributeList([%s]);\n", implode(",\n", $export)));
			}
		}

		protected function element(INode $element, ICompiler $compiler) {
			$destination = $compiler->getDestination();
			if ($element->isLeaf()) {
				parent::element($element, $compiler);
				return;
			}
			$destination->write("\t\t\t\$this->stack->push(\$control);\n");
			parent::element($element, $compiler);
			$destination->write("\t\t\t\$control = \$this->stack->pop();\n");
		}
	}

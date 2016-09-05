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
			$this->writeCreateControl($destination, $this->control);
			$this->writeTextValue($element, $destination, $compiler);
			$attributeList = $this->getAttributeList($element, $compiler);
			unset($attributeList['value']);
			$this->writeAttributeList($attributeList, $destination);
			$this->element($element, $compiler);
		}

		protected function writeCreateControl(IFile $destination, string $control) {
			$destination->write(sprintf("\t\t\t\$parent->addControl(\$current = \$this->container->create('%s'));\n", $control));
		}

		protected function writeTextValue(INode $root, IFile $destination, ICompiler $compiler) {
			if ($root->isLeaf() && ($text = $root->getValue($root->getAttribute('value'))) !== null) {
				$destination->write(sprintf("\t\t\t\$current->setText(%s);\n", $compiler->delimite($text)));
			}
		}

		protected function writeAttributeList(array $attributeList, IFile $destination) {
			if (empty($attributeList) === false) {
				$export = [];
				foreach ($attributeList as $name => $value) {
					$export[] = "'" . $name . "' => " . $value;
				}
				$destination->write(sprintf("\t\t\t\$current->setAttributeList([%s]);\n", implode(",\n", $export)));
			}
		}

		protected function element(INode $element, ICompiler $compiler) {
			$destination = $compiler->getDestination();
			if ($element->isLeaf()) {
				parent::element($element, $compiler);
				return;
			}
			$destination->write("\t\t\t\$this->stack->push(\$current);\n");
			parent::element($element, $compiler);
			$destination->write("\t\t\t\$current = \$this->stack->pop();\n");
		}
	}

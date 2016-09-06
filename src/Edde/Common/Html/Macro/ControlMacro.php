<?php
	declare(strict_types = 1);

	namespace Edde\Common\Html\Macro;

	use Edde\Api\Control\IControl;
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

			$destination->write(sprintf("\t\t\t/** %s */\n", $element->getPath()));
			$destination->write(sprintf("\t\t\t\$controlList[%s] = function(%s \$root) use(&\$controlList, &\$stash) {\n", $compiler->delimite($element->getMeta('control')), IControl::class));
			$destination->write(sprintf("\t\t\t\t\$root->addControl(\$control = \$this->container->create(%s));\n", $compiler->delimite($this->control)));
			$this->writeTextValue($element, $destination, $compiler);
			$this->onControl($macro, $element, $compiler);
			$this->writeAttributeList($this->getAttributeList($element, $compiler), $destination);
			foreach ($element->getNodeList() as $node) {
				$destination->write(sprintf("\t\t\t\t\$controlList[%s](\$control);\n", $compiler->delimite($node->getMeta('control'))));
			}
			$destination->write("\t\t\t};\n");
			$this->element($element, $compiler);
		}

		protected function writeTextValue(INode $root, IFile $destination, ICompiler $compiler) {
			if ($root->isLeaf() && ($text = $root->getValue($this->extractAttribute($root, 'value'))) !== null) {
				$destination->write(sprintf("\t\t\t\t\$control->setText(%s);\n", $compiler->delimite($text)));
			}
		}

		protected function extractAttribute(INode $node, string $name) {
			$attributeList = $node->getAttributeList();
			$value = $node->getAttribute($name);
			unset($attributeList[$name]);
			$node->setAttributeList($attributeList);
			return $value;
		}

		protected function onControl(INode $macro, INode $element, ICompiler $compiler) {
		}

		protected function writeAttributeList(array $attributeList, IFile $destination) {
			if (empty($attributeList) === false) {
				$export = [];
				foreach ($attributeList as $name => $value) {
					$export[] = "'" . $name . "' => " . $value;
				}
				$destination->write(sprintf("\t\t\t\t\$control->setAttributeList([%s]);\n", implode(",\n", $export)));
			}
		}
	}

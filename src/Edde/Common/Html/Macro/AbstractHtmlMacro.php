<?php
	declare(strict_types = 1);

	namespace Edde\Common\Html\Macro;

	use Edde\Api\Control\IControl;
	use Edde\Api\File\IFile;
	use Edde\Api\Node\INode;
	use Edde\Api\Template\ICompiler;
	use Edde\Common\Template\AbstractMacro;

	abstract class AbstractHtmlMacro extends AbstractMacro {
		public function lambda(INode $macro, INode $element, ICompiler $compiler) {
			$this->start($macro, $element, $compiler);
			$this->dependencies($macro, $compiler);
			$this->end($macro, $element, $compiler);
		}

		protected function start(INode $macro, INode $element, ICompiler $compiler) {
			$destination = $compiler->getDestination();
			$destination->write(sprintf("\t\t\t/** %s (%s) */\n", $macro->getPath(), $element->getPath()));
			$destination->write(sprintf("\t\t\t\$this->addControl(%s, function(%s \$root) use(&\$stash): %s {\n", $compiler->delimite($macro->getMeta('control')), IControl::class, IControl::class));
			$destination->write("\t\t\t\t\$control = \$root;\n");
		}

		protected function dependencies(INode $macro, ICompiler $compiler) {
			$destination = $compiler->getDestination();
			foreach ($macro->getNodeList() as $node) {
				$destination->write(sprintf("\t\t\t\t/** %s */\n", $node->getPath()));
				$destination->write(sprintf("\t\t\t\t\$current = \$this->controlList[%s](\$control);\n", $compiler->delimite($node->getMeta('control'))));
			}
		}

		protected function end(INode $macro, INode $element, ICompiler $compiler, $chilren = true) {
			$destination = $compiler->getDestination();
			$destination->write("\t\t\t\treturn \$control;\n");
			$destination->write("\t\t\t});\n");
			$chilren ? $this->element($macro, $compiler) : null;
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

		protected function writeAttributeList(array $attributeList, IFile $destination) {
			if (empty($attributeList) === false) {
				$export = [];
				foreach ($attributeList as $name => $value) {
					$export[] = "'" . $name . "' => " . $value;
				}
				$destination->write(sprintf("\t\t\t\t\$control->setAttributeList([%s]);\n", implode(', ', $export)));
			}
		}
	}

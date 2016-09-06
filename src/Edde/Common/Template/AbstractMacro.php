<?php
	declare(strict_types = 1);

	namespace Edde\Common\Template;

	use Edde\Api\Node\INode;
	use Edde\Api\Template\ICompiler;
	use Edde\Api\Template\IMacro;
	use Edde\Api\Template\MacroException;
	use Edde\Common\AbstractObject;

	abstract class AbstractMacro extends AbstractObject implements IMacro {
		/**
		 * @var array
		 */
		protected $macroList = [];

		/**
		 * @param array $macroList
		 */
		public function __construct(array $macroList) {
			$this->macroList = $macroList;
		}

		public function getMacroList(): array {
			return $this->macroList;
		}

		public function variable(string $string, ICompiler $compiler) {
			return null;
		}

		protected function getAttributeList(INode $node, ICompiler $compiler) {
			$attributeList = [];
			foreach ($node->getAttributeList() as $name => $value) {
				$attributeList[$name] = $compiler->delimite($value);
			}
			return $attributeList;
		}

		protected function element(INode $element, ICompiler $compiler) {
			foreach ($element->getNodeList() as $node) {
				$compiler->macro($node, $node);
			}
		}

		protected function checkLeaf(INode $macro, INode $element) {
			if ($element->isLeaf() === false) {
				throw new MacroException(sprintf('Macro [%s] in [%s] must not have children.', $macro->getName(), $element->getPath()));
			}
		}

		protected function checkNotLeaf(INode $macro, INode $element) {
			if ($element->isLeaf()) {
				throw new MacroException(sprintf('Macro [%s] in [%s] must have children nodes.', $macro->getName(), $element->getPath()));
			}
		}

		protected function checkAttribute(INode $macro, INode $element, ...$attributeList) {
			foreach ($attributeList as $attribute) {
				if ($macro->hasAttribute($attribute) === false) {
					throw new MacroException(sprintf('Missing attribute "%s" in macro [%s] at [%s].', $attribute, $macro->getName(), $element->getPath()));
				}
			}
		}

		protected function checkElementAttribute(INode $macro, INode $element, ...$attributeList) {
			foreach ($attributeList as $attribute) {
				if ($element->hasAttribute($attribute) === false) {
					throw new MacroException(sprintf('Missing attribute "%s" in element [%s] for macro [%s].', $attribute, $element->getPath(), $macro->getName()));
				}
			}
		}

		protected function checkValue(INode $macro, INode $element) {
			if ($macro->getValue() === null) {
				throw new MacroException(sprintf('Missing value of macro [%s] at [%s].', $macro->getName(), $element->getPath()));
			}
		}
	}

<?php
	declare(strict_types = 1);

	namespace Edde\Common\Template;

	use Edde\Api\Node\INode;
	use Edde\Api\Template\ICompiler;
	use Edde\Api\Template\IMacro;
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
				$attributeList[$name] = $compiler->value($value);
			}
			return $attributeList;
		}

		protected function macro(INode $root, ICompiler $compiler) {
			foreach ($root->getNodeList() as $node) {
				$compiler->macro($node, $compiler);
			}
		}
	}

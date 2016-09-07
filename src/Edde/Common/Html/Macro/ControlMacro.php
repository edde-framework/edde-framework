<?php
	declare(strict_types = 1);

	namespace Edde\Common\Html\Macro;

	use Edde\Api\Control\IControl;
	use Edde\Api\Node\INode;
	use Edde\Api\Template\ICompiler;

	class ControlMacro extends AbstractHtmlMacro {
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
			$destination->write("\t\t\t\treturn \$control;\n");
			$destination->write("\t\t\t};\n");
			$this->element($element, $compiler);
		}

		protected function onControl(INode $macro, INode $element, ICompiler $compiler) {
		}
	}

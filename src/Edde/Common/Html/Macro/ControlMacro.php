<?php
	declare(strict_types = 1);

	namespace Edde\Common\Html\Macro;

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

			$this->start($macro, $element, $compiler);
			$destination->write(sprintf("\t\t\t\t\$root->addControl(\$control = \$this->container->create(%s));\n", $compiler->delimite($this->control)));
			$this->writeTextValue($element, $destination, $compiler);
			$this->onControl($macro, $element, $compiler);
			$this->writeAttributeList($this->getAttributeList($element, $compiler), $destination);
			foreach ($element->getNodeList() as $node) {
				$destination->write(sprintf("\t\t\t\t\$controlList[%s](\$control);\n", $compiler->delimite($node->getMeta('control'))));
			}
			$this->end($macro, $element, $compiler);
			$this->element($element, $compiler);
		}

		protected function onControl(INode $macro, INode $element, ICompiler $compiler) {
		}
	}

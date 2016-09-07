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
			$destination->write(sprintf("\t\t\t\t\$root->addControl(\$this->current = \$control = \$this->container->create(%s));\n", $compiler->delimite($this->control)));
			$this->writeTextValue($macro, $destination, $compiler);
			$this->onControl($macro, $element, $compiler);
			$this->writeAttributeList($this->getAttributeList($macro, $compiler), $destination);
			$this->dependencies($macro, $compiler);
			$this->end($macro, $element, $compiler);
		}

		protected function onControl(INode $macro, INode $element, ICompiler $compiler) {
		}
	}

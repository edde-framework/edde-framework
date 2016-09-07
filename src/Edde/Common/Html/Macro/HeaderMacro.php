<?php
	declare(strict_types = 1);

	namespace Edde\Common\Html\Macro;

	use Edde\Api\Node\INode;
	use Edde\Api\Template\ICompiler;
	use Edde\Common\Html\HeaderControl;

	class HeaderMacro extends ControlMacro {
		public function __construct() {
			parent::__construct([
				'h1',
				'h2',
				'h3',
				'h4',
				'h5',
				'h6',
			], HeaderControl::class);
		}

		protected function onControl(INode $macro, INode $element, ICompiler $compiler) {
			$destination = $compiler->getDestination();
			$destination->write(sprintf("\t\t\t\$control->setTag('%s');\n", $element->getName()));
		}
	}

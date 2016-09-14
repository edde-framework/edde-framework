<?php
	declare(strict_types = 1);

	namespace Edde\Common\Html\Macro;

	use Edde\Api\Node\INode;
	use Edde\Api\Template\ICompiler;
	use Edde\Common\Html\Tag\ButtonControl;

	class ButtonMacro extends ControlMacro {
		public function __construct() {
			parent::__construct([
				'button',
			], ButtonControl::class);
		}

		protected function onControl(INode $macro, INode $element, ICompiler $compiler) {
			$this->checkAttribute($macro, $element, 'action');
			$destination = $compiler->getDestination();
			$destination->write(sprintf("\t\t\t\t\$control->setAction(%s);\n", $compiler->delimite($this->extractAttribute($macro, 'action'))));
		}
	}

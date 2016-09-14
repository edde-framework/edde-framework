<?php
	declare(strict_types = 1);

	namespace Edde\Common\Html\Macro;

	use Edde\Api\Node\INode;
	use Edde\Api\Template\ICompiler;
	use Edde\Api\Template\MacroException;
	use Edde\Common\Html\Tag\ButtonControl;

	class ButtonMacro extends ControlMacro {
		public function __construct() {
			parent::__construct([
				'button',
			], ButtonControl::class);
		}

		protected function onControl(INode $macro, INode $element, ICompiler $compiler) {
			$this->checkAttribute($macro, $element, 'action');
			if (strrpos($action = $this->extractAttribute($macro, 'action'), '()', 0) === false) {
				throw new MacroException(sprintf('Action [%s] attribute needs to have () at the end.', $action));
			}
			$destination = $compiler->getDestination();
			$destination->write(sprintf("\t\t\t\t\$control->setAction([\$this->root, %s]);\n", $compiler->delimite(str_replace('()', '', $action))));
		}
	}

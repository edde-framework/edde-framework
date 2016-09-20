<?php
	declare(strict_types = 1);

	namespace Edde\Common\Html\Macro;

	use Edde\Api\Node\INode;
	use Edde\Api\Template\ICompiler;
	use Edde\Common\Template\AbstractMacro;

	/**
	 * Root control macro for template generation.
	 */
	class ControlMacro extends AbstractMacro {
		public function __construct() {
			parent::__construct('control');
		}

		public function macro(INode $macro, ICompiler $compiler) {
			if ($compiler->isLayout() === false) {
				return false;
			}
			return true;
		}
	}

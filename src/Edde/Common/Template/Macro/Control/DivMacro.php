<?php
	declare(strict_types = 1);

	namespace Edde\Common\Template\Macro\Control;

	use Edde\Common\Html\DivControl;

	class DivMacro extends AbstractControlMacro {
		public function __construct() {
			parent::__construct([
				'div',
			], DivControl::class);
		}
	}

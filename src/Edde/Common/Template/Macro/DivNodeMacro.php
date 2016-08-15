<?php
	declare(strict_types = 1);

	namespace Edde\Common\Template\Macro;

	use Edde\Common\Html\DivControl;

	class DivNodeMacro extends AbstractControlMacro {
		public function __construct() {
			parent::__construct([
				'div',
			], DivControl::class);
		}
	}

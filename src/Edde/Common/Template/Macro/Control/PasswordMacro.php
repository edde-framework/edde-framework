<?php
	declare(strict_types = 1);

	namespace Edde\Common\Template\Macro\Control;

	use Edde\Common\Html\Value\PasswordInputControl;

	class PasswordMacro extends AbstractControlMacro {
		public function __construct() {
			parent::__construct(['password'], PasswordInputControl::class);
		}
	}

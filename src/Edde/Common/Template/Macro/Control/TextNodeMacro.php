<?php
	declare(strict_types = 1);

	namespace Edde\Common\Template\Macro\Control;

	use Edde\Common\Html\Value\TextInputControl;

	class TextNodeMacro extends AbstractControlMacro {
		public function __construct() {
			parent::__construct(['text'], TextInputControl::class);
		}
	}

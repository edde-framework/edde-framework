<?php
	declare(strict_types = 1);

	namespace Edde\Common\Template\Macro\Control;

	use Edde\Common\Html\SpanControl;

	class SpanNodeMacro extends AbstractControlMacro {
		public function __construct() {
			parent::__construct([
				'span',
			], SpanControl::class);
		}
	}

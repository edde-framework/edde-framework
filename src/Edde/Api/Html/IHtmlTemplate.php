<?php
	declare(strict_types = 1);

	namespace Edde\Api\Html;

	use Edde\Api\Control\IControl;

	interface IHtmlTemplate {
		public function template(IControl $root);
	}

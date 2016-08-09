<?php
	declare(strict_types = 1);

	namespace Edde\Common\Template\Filter;

	use Edde\Api\Html\IHtmlControl;
	use Edde\Common\Filter\AbstractFilter;

	class BindAttributeFilter extends AbstractFilter {
		public function input($value, IHtmlControl $htmlControl) {
			$htmlControl->setAttribute('data-bind', $value);
			return false;
		}
	}

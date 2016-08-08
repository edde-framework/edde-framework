<?php
	declare(strict_types = 1);

	namespace Edde\Common\Template\Filter;

	use Edde\Api\Html\IHtmlControl;
	use Edde\Common\Filter\AbstractFilter;

	class ActionAttributeFilter extends AbstractFilter {
		public function input($value, IHtmlControl $htmlControl) {
			$parent = $htmlControl->getRoot();
			$htmlControl->setAttribute('data-control', get_class($parent));
			$htmlControl->setAttribute('data-action', $value);
			return false;
		}
	}

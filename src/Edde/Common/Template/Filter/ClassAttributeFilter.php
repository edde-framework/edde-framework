<?php
	declare(strict_types = 1);

	namespace Edde\Common\Template\Filter;

	use Edde\Common\Filter\AbstractFilter;

	class ClassAttributeFilter extends AbstractFilter {
		public function filter($value, ...$parameterList) {
			return explode(' ', $value);
		}
	}

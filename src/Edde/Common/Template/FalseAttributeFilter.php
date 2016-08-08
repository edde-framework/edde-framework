<?php
	declare(strict_types = 1);

	namespace Edde\Common\Template;

	use Edde\Common\Filter\AbstractFilter;

	class FalseAttributeFilter extends AbstractFilter {
		public function filter($value, ...$parameterList) {
			return false;
		}
	}

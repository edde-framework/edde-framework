<?php
	declare(strict_types = 1);

	namespace Edde\Common\Filter;

	use Edde\Api\Filter\IFilter;
	use Edde\Common\AbstractObject;

	class AbstractFilter extends AbstractObject implements IFilter {
		public function filter($value, ...$parameterList) {
			return call_user_func_array([
				$this,
				'input',
			], array_merge([$value], $parameterList));
		}
	}

<?php
	declare(strict_types=1);

	namespace Edde\Common\Filter;

	use Edde\Api\Crypt\LazyCryptEngineTrait;

	/**
	 * Generate guid; if the value is set, it is used as a seed.
	 */
	class GuidFilter extends AbstractFilter {
		use LazyCryptEngineTrait;

		/**
		 * @inheritdoc
		 */
		public function filter($value, ...$parameterList) {
			return $this->cryptEngine->guid($value);
		}
	}

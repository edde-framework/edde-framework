<?php
	declare(strict_types = 1);

	namespace Edde\Common\Filter;

	use Edde\Api\Crypt\ICryptEngine;
	use Edde\Common\Container\LazyInjectTrait;

	/**
	 * Generate guid; if the value is set, it is used as a seed.
	 */
	class GuidFilter extends AbstractFilter {
		use LazyInjectTrait;
		/**
		 * @var ICryptEngine
		 */
		protected $cryptEngine;

		public function lazyCryptEngine(ICryptEngine $cryptEngine) {
			$this->cryptEngine = $cryptEngine;
		}

		public function filter($value, ...$parameterList) {
			return $this->cryptEngine->guid($value);
		}
	}

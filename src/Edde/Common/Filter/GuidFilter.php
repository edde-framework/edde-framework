<?php
	declare(strict_types = 1);

	namespace Edde\Common\Filter;

	use Edde\Api\Container\ILazyInject;
	use Edde\Api\Crypt\ICryptEngine;

	/**
	 * Generate guid; if the value is set, it is used as a seed.
	 */
	class GuidFilter extends AbstractFilter implements ILazyInject {
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

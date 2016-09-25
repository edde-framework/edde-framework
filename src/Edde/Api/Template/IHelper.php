<?php
	declare(strict_types = 1);

	namespace Edde\Api\Template;

	/**
	 * Helper is used for attribute value filtering.
	 */
	interface IHelper {
		/**
		 * when null is returned, next helper should be executed
		 *
		 * @param ICompiler $compiler
		 * @param mixed $value
		 * @param array ...$parameterList
		 *
		 * @return mixed|null
		 */
		public function helper(ICompiler $compiler, $value, ...$parameterList);
	}

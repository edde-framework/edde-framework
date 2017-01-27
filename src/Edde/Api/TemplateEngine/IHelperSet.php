<?php
	declare(strict_types = 1);

	namespace Edde\Api\TemplateEngine;

	/**
	 * Set of helpers.
	 */
	interface IHelperSet {
		/**
		 * @param IHelper $helper
		 *
		 * @return IHelperSet
		 */
		public function registerHelper(IHelper $helper): IHelperSet;

		/**
		 * @return IHelper[]
		 */
		public function getHelperList(): array;
	}

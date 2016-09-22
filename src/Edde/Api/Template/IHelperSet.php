<?php
	declare(strict_types = 1);

	namespace Edde\Api\Template;

	use Edde\Api\Usable\IUsable;

	/**
	 * Set of helpers.
	 */
	interface IHelperSet extends IUsable {
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

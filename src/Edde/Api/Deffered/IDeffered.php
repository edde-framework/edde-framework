<?php
	declare(strict_types = 1);

	namespace Edde\Api\Deffered;

	/**
	 * Any class can be usable in terms of passive behavior until "touch". This was originally developed from Service interface.
	 */
	interface IDeffered {
		/**
		 * callback executed before {@see self::use()}
		 *
		 * @param callable $callback
		 *
		 * @return $this
		 */
		public function registerOnUse(callable $callback);

		/**
		 * prepare for the first usage
		 *
		 * @return $this
		 */
		public function use ();

		/**
		 * has been deffered already used (prepared)?
		 *
		 * @return bool
		 */
		public function isUsed();
	}

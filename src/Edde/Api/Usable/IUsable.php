<?php
	namespace Edde\Api\Usable;

	/**
	 * Any class can be usable in terms of passive behavior until "touch". This was originally developed from Service interface.
	 */
	interface IUsable {
		/**
		 * callback executed before {@see self::usse()}
		 *
		 * @param callable $callback
		 *
		 * @return $this
		 */
		public function onSetup(callable $callback);

		/**
		 * callback executed after {@see self::usse()}
		 *
		 * @param callable $callback
		 *
		 * @return $this
		 */
		public function onUse(callable $callback);

		/**
		 * general purpose callback executed when usable is loaded (used); if it is already loaded, callback is executed immediately
		 *
		 * @param callable $callback
		 *
		 * @return $this
		 */
		public function onLoaded(callable $callback);

		/**
		 * prepare for the first usage
		 *
		 * @return $this
		 */
		public function usse();

		/**
		 * is usable already used?
		 *
		 * @return bool
		 */
		public function isUsed();
	}

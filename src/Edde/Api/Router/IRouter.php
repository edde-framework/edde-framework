<?php
	namespace Edde\Api\Router;

	use Edde\Api\Usable\IUsable;

	interface IRouter extends IUsable {
		/**
		 * can this router handle current request (can be arbitrary, e.g. cli run)
		 *
		 * @return IRoute|null
		 */
		public function route();
	}

<?php
	declare(strict_types = 1);

	namespace Edde\Api\Application;

	use Edde\Api\Usable\IUsable;

	/**
	 * Single application implementation; per project should be exactly one instance (implementation) of this interface.
	 */
	interface IApplication extends IUsable {
		/**
		 * execute main "loop" of application (process the given request)
		 *
		 * @return mixed
		 */
		public function run();
	}

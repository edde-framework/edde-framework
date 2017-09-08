<?php
	declare(strict_types=1);

	namespace Edde\Api\Container;

	use Edde\Api\Config\IConfigurable;

	/**
	 * Proxy is simple class for deferring dependency creation; when there is need to
	 * register class to some service, not all times it's needed to create instance of
	 * it. That's reason for this class.
	 */
	interface IProxy extends IConfigurable {
		/**
		 * execute deferred creation
		 *
		 * @return mixed
		 */
		public function proxy();
	}

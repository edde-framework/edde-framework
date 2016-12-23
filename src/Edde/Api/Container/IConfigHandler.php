<?php
	declare(strict_types = 1);

	namespace Edde\Api\Container;

	/**
	 * This interface is general way how to implement service configuration (or configuration of almost enything).
	 */
	interface IConfigHandler {
		/**
		 * run setup over the given instance
		 *
		 * @param mixed $instance
		 *
		 * @return IConfigHandler
		 */
		public function setup($instance): IConfigHandler;
	}

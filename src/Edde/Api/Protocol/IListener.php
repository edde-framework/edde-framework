<?php
	declare(strict_types=1);

	namespace Edde\Api\Protocol;

	interface IListener {
		/**
		 * return/generate list of listeners for EventBus
		 *
		 * @return \Traversable|array
		 */
		public function getListenerList();
	}

<?php
	declare(strict_types=1);

	namespace Edde\Api\Lock;

	/**
	 * Lock descriptor; could be used to actually control a lock.
	 */
	interface ILock {
		/**
		 * get lock name/id
		 *
		 * @return string
		 */
		public function getId(): string;

		/**
		 * whoe made a lock
		 *
		 * @return string
		 */
		public function getSource(): string;
	}

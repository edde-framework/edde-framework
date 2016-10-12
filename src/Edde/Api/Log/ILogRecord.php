<?php
	declare(strict_types = 1);

	namespace Edde\Api\Log;

	/**
	 * Every log record must implement this interface.
	 */
	interface ILogRecord {
		/**
		 * compute target log item
		 *
		 * @return string
		 */
		public function getLog(): string;

		/**
		 * return array of tags for this log record
		 *
		 * @return array|null
		 */
		public function getTagList();
	}

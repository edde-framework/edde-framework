<?php
	declare(strict_types = 1);

	namespace Edde\Api\Log;

	use Edde\Api\Deffered\IDeffered;
	use Edde\Api\Filter\IFilter;

	/**
	 * Implementation of a log service.
	 */
	interface ILogService extends IDeffered {
		/**
		 * bind the given filter on the tag list; this can be useful for hiding/masking confidental data (passwords, ...)
		 *
		 * @param array $tagList
		 * @param IFilter $filter
		 *
		 * @return ILogService
		 */
		public function registerContentFilter(array $tagList, IFilter $filter): ILogService;

		/**
		 * shortcut for record();
		 *
		 * @param string $log
		 * @param array $tagList
		 *
		 * @return ILogService
		 */
		public function log(string $log, array $tagList = []): ILogService;

		/**
		 * @param ILogRecord $logRecord
		 *
		 * @return ILogService
		 */
		public function record(ILogRecord $logRecord): ILogService;

		/**
		 * adds informative tag
		 *
		 * @param string $log
		 * @param array $tagList
		 *
		 * @return ILogService
		 */
		public function info(string $log, array $tagList = []): ILogService;

		/**
		 * adds warning tag
		 *
		 * @param string $log
		 * @param array $tagList
		 *
		 * @return ILogService
		 */
		public function warning(string $log, array $tagList = []): ILogService;

		/**
		 * adds error tag
		 *
		 * @param string $log
		 * @param array $tagList
		 *
		 * @return ILogService
		 */
		public function error(string $log, array $tagList = []): ILogService;

		/**
		 * adds critical tag
		 *
		 * @param string $log
		 * @param array $tagList
		 *
		 * @return ILogService
		 */
		public function critical(string $log, array $tagList = []): ILogService;
	}

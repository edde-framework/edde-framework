<?php
	declare(strict_types = 1);

	namespace Edde\Api\Log;

	use Edde\Api\Deffered\IDeffered;
	use Edde\Api\Filter\IFilter;

	/**
	 * Implementation of a log service.
	 */
	interface ILogService extends ILog, IDeffered {
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
		 * register the given log to the given set of tags
		 *
		 * @param array $tagList
		 * @param ILog $log
		 *
		 * @return ILogService
		 */
		public function registerLog(array $tagList, ILog $log): ILogService;
	}

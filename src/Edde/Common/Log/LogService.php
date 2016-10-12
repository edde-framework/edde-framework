<?php
	declare(strict_types = 1);

	namespace Edde\Common\Log;

	use Edde\Api\Filter\IFilter;
	use Edde\Api\Log\ILog;
	use Edde\Api\Log\ILogRecord;
	use Edde\Api\Log\ILogService;
	use Edde\Common\Deffered\DefferedTrait;

	/**
	 * Default implementation of log service.
	 */
	class LogService extends AbstractLog implements ILogService {
		use DefferedTrait;
		/**
		 * @var IFilter[]
		 */
		protected $contentFilterList = [];
		/**
		 * @var ILog[]
		 */
		protected $logList = [];

		/**
		 * @inheritdoc
		 */
		public function registerContentFilter(array $tagList, IFilter $filter): ILogService {
			foreach ($tagList as $tag) {
				$this->contentFilterList[$tag] = $filter;
			}
			return $this;
		}

		/**
		 * @inheritdoc
		 */
		public function registerLog(array $tagList, ILog $log): ILogService {
			foreach ($tagList as $tag) {
				$this->logList[$tag] = $log;
			}
			return $this;
		}

		/**
		 * @inheritdoc
		 */
		public function record(ILogRecord $logRecord): ILog {
			$log = $logRecord->getLog();
			$tagList = array_unique($logRecord->getTagList());
			foreach ($tagList as $tag) {
				$log = isset($this->contentFilterList[$tag]) ? $this->contentFilterList[$tag]->filter($log) : $log;
			}
			/**
			 * second run because all filter has been applied
			 */
			foreach ($tagList as $tag) {
				isset($this->logList[$tag]) ? $this->logList[$tag]->record($logRecord) : null;
			}
			return $this;
		}
	}

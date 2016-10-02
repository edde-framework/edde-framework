<?php
	declare(strict_types = 1);

	namespace Edde\Common\Log;

	use Edde\Api\Filter\IFilter;
	use Edde\Api\Log\ILogRecord;
	use Edde\Api\Log\ILogService;
	use Edde\Common\Deffered\AbstractDeffered;

	/**
	 * Default implementation of log service.
	 */
	class LogService extends AbstractDeffered implements ILogService {
		/**
		 * @var IFilter[]
		 */
		protected $contentFilterList = [];

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
		public function info(string $log, array $tagList = []): ILogService {
			$tagList[] = __FUNCTION__;
			return $this->log($log, $tagList);
		}

		/**
		 * @inheritdoc
		 */
		public function log(string $log, array $tagList = []): ILogService {
			return $this->record(new LogRecord($log, $tagList));
		}

		/**
		 * @inheritdoc
		 */
		public function record(ILogRecord $logRecord): ILogService {
			$log = $logRecord->getLog();
			foreach ($logRecord->getTagList() as $tag) {
				$log = isset($this->contentFilterList[$tag]) ? $this->contentFilterList[$tag]->filter($log) : $log;
			}
			throw new \Exception('not implemented yet: log delivery based on tags');
			return $this;
		}

		/**
		 * @inheritdoc
		 */
		public function warning(string $log, array $tagList = []): ILogService {
			$tagList[] = __FUNCTION__;
			return $this->log($log, $tagList);
		}

		/**
		 * @inheritdoc
		 */
		public function error(string $log, array $tagList = []): ILogService {
			$tagList[] = __FUNCTION__;
			return $this->log($log, $tagList);
		}

		/**
		 * @inheritdoc
		 */
		public function critical(string $log, array $tagList = []): ILogService {
			$tagList[] = __FUNCTION__;
			return $this->log($log, $tagList);
		}
	}

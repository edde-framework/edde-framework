<?php
	declare(strict_types = 1);

	namespace Edde\Common\Log;

	use Edde\Api\Log\ILogRecord;
	use Edde\Common\AbstractObject;

	class LogRecord extends AbstractObject implements ILogRecord {
		/**
		 * @var string
		 */
		protected $log;
		/**
		 * @var array
		 */
		protected $tagList;

		/**
		 * @param string $log
		 * @param array $tagList
		 */
		public function __construct($log, array $tagList) {
			$this->log = $log;
			$this->tagList = $tagList;
		}

		/**
		 * @inheritdoc
		 */
		public function getLog(): string {
			return $this->log;
		}

		/**
		 * @inheritdoc
		 */
		public function getTagList(): array {
			return $this->tagList;
		}
	}

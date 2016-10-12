<?php
	declare(strict_types = 1);

	namespace Edde\Common\Log;

	use Edde\Api\Log\ILogRecord;
	use Edde\Common\AbstractObject;

	/**
	 * Simple log recorord; holds record without any modifications.
	 */
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
		 * A blonde rings up an airline.
		 * She asks, "How long are your flights from America to England?"
		 * The woman on the other end of the phone says, "Just a minute..."
		 * The blonde says, "Thanks!" and hangs up the phone.
		 *
		 * @param string $log
		 * @param array $tagList
		 */
		public function __construct($log, array $tagList = null) {
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
		public function getTagList() {
			return $this->tagList;
		}
	}

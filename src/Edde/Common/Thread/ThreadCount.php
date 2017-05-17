<?php
	declare(strict_types=1);

	namespace Edde\Common\Thread;

	use Edde\Api\Thread\IThreadCount;

	class ThreadCount extends AbstractThreadCount {
		/**
		 * @var int
		 */
		protected $max;

		public function __construct(int $max = 4) {
			$this->max = $max;
		}

		/**
		 * @inheritdoc
		 */
		public function canExecute(): bool {
			return $this->count <= $this->max;
		}

		/**
		 * @inheritdoc
		 */
		public function update(): IThreadCount {
			return $this;
		}
	}

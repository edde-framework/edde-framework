<?php
	declare(strict_types=1);

	namespace Edde\Common\Thread;

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
			$this->load();
			return $this->count <= $this->max;
		}
	}

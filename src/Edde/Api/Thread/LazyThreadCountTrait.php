<?php
	declare(strict_types=1);

	namespace Edde\Api\Thread;

	trait LazyThreadCountTrait {
		/**
		 * @var IThreadCount
		 */
		protected $threadCount;

		/**
		 * @param IThreadCount $threadCount
		 */
		public function lazyThreadCount(IThreadCount $threadCount) {
			$this->threadCount = $threadCount;
		}
	}

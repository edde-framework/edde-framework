<?php
	declare(strict_types=1);

	namespace Edde\Common\Job;

	use Edde\Api\Job\IJob;
	use Edde\Api\Job\IJobQueue;
	use Edde\Api\Store\LazyStoreTrait;

	class JobQueue extends AbstractJobQueue {
		use LazyStoreTrait;

		/**
		 * @inheritdoc
		 */
		public function queue(IJob $job): IJobQueue {
			$this->store->append(static::class, $job);
			return $this;
		}

		/**
		 * @inheritdoc
		 */
		public function hasJob(): bool {
			return $this->store->has(static::class);
		}

		/**
		 * @inheritdoc
		 */
		public function dequeue() {
			/** @var $job IJob */
			foreach ($this->store->pickup(static::class) as $job) {
				yield $job;
			}
		}
	}

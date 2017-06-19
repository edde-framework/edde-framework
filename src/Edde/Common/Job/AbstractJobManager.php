<?php
	declare(strict_types=1);

	namespace Edde\Common\Job;

	use Edde\Api\Job\IJob;
	use Edde\Api\Job\IJobManager;
	use Edde\Api\Job\IJobQueue;
	use Edde\Api\Job\LazyJobQueueTrait;

	abstract class AbstractJobManager extends AbstractJobQueue implements IJobManager {
		use LazyJobQueueTrait;

		/**
		 * @inheritdoc
		 */
		public function queue(IJob $job): IJobQueue {
			$this->jobQueue->queue($job);
			return $this;
		}

		/**
		 * @inheritdoc
		 */
		public function dequeue(): IJobQueue {
			$this->execute($this->jobQueue);
			return $this;
		}

		/**
		 * @inheritdoc
		 */
		public function execute(IJobQueue $jobQueue): IJobManager {
			foreach ($jobQueue->dequeue() as $job) {
				$job->execute();
			}
			return $this;
		}
	}

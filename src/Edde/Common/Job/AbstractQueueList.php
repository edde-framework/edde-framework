<?php
	declare(strict_types=1);

	namespace Edde\Common\Job;

	use Edde\Api\Job\IJob;
	use Edde\Api\Job\IJobQueue;
	use Edde\Api\Job\IQueueList;
	use Edde\Api\Job\JobQueueException;

	abstract class AbstractQueueList extends AbstractJobQueue implements IQueueList {
		/**
		 * @var IJobQueue[]
		 */
		protected $jobQueueList = [];

		/**
		 * @inheritdoc
		 */
		public function addJobQueue(IJobQueue $jobQueue): IQueueList {
			$this->jobQueueList[] = $jobQueue;
			return $this;
		}

		/**
		 * @inheritdoc
		 */
		public function queue(IJob $job): IJobQueue {
			throw new JobQueueException(sprintf('Cannot queue job to [%s]! Use concrete queue [%s] instead of general queue list.', IQueueList::class, IJobQueue::class));
		}

		/**
		 * @inheritdoc
		 */
		public function hasJob(): bool {
			foreach ($this->jobQueueList as $jobQueue) {
				if ($jobQueue->hasJob()) {
					return true;
				}
			}
			return false;
		}
	}

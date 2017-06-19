<?php
	declare(strict_types=1);

	namespace Edde\Common\Job;

	use Edde\Api\Job\IJob;
	use Edde\Api\Job\IJobManager;
	use Edde\Api\Job\IJobQueue;
	use Edde\Api\Job\LazyJobQueueTrait;
	use Edde\Common\Config\ConfigurableTrait;
	use Edde\Common\Object;

	abstract class AbstractJobManager extends Object implements IJobManager {
		use LazyJobQueueTrait;
		use ConfigurableTrait;

		/**
		 * @inheritdoc
		 */
		public function queue(IJob $job): IJobManager {
			$this->jobQueue->queue($job);
			return $this;
		}

		/**
		 * @inheritdoc
		 */
		public function dequeue(IJobQueue $jobQueue): IJobManager {
			foreach ($jobQueue->dequeue() as $job) {
				$job->execute();
			}
			return $this;
		}

		/**
		 * @inheritdoc
		 */
		public function execute(): IJobManager {
			$this->dequeue($this->jobQueue);
			return $this;
		}
	}

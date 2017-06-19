<?php
	declare(strict_types=1);

	namespace Edde\Api\Job;

	use Edde\Api\Config\IConfigurable;

	interface IJobManager extends IConfigurable {
		/**
		 * enqueue the given job to the default job queue
		 *
		 * @param IJob $job
		 *
		 * @return IJobManager
		 */
		public function queue(IJob $job): IJobManager;

		/**
		 * dequeue the given job queue
		 *
		 * @param IJobQueue|IJob[] $jobQueue
		 *
		 * @return IJobManager
		 */
		public function dequeue(IJobQueue $jobQueue): IJobManager;

		/**
		 * dequeue current IJobQueue (used from a dependency)
		 *
		 * @return IJobManager
		 */
		public function execute(): IJobManager;
	}

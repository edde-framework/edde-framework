<?php
	declare(strict_types=1);

	namespace Edde\Api\Job;

	interface IJobManager extends IJobQueue {
		/**
		 * dequeue current IJobQueue (used from a dependency)
		 *
		 * @param IJobQueue $jobQueue
		 *
		 * @return IJobManager
		 */
		public function execute(IJobQueue $jobQueue): IJobManager;
	}

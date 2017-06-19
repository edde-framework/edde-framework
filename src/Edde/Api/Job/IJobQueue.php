<?php
	declare(strict_types=1);

	namespace Edde\Api\Job;

	use Edde\Api\Config\IConfigurable;

	/**
	 * This is general source for jobs.
	 */
	interface IJobQueue extends IConfigurable {
		/**
		 * enqueue the given job
		 *
		 * @param IJob $job
		 *
		 * @return IJobQueue
		 */
		public function queue(IJob $job): IJobQueue;

		/**
		 * are there some pending jobs?
		 *
		 * @return bool
		 */
		public function hasJob(): bool;

		/**
		 * return generator/traversable of jobs
		 *
		 * @return array|\Traversable|IJob[]
		 */
		public function dequeue();
	}

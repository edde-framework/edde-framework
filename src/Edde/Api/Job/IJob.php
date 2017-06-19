<?php
	declare(strict_types=1);

	namespace Edde\Api\Job;

	use Edde\Api\Config\IConfigurable;

	/**
	 * Job is individual small piece of code that should be executed in quite short time (e.g. up to 5min); bigger long running tasks
	 * should be broken to smaller jobs to be more maintainable and eventually able to be processed in parallel.
	 *
	 * For example a job could take computation of subset of record of table, not whole table.
	 */
	interface IJob extends IConfigurable {
		/**
		 * execute the given job; job itself has to maintain all progress of the job, such as failures and catching result.
		 *
		 * @return IJob
		 */
		public function execute(): IJob;
	}

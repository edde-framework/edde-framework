<?php
	declare(strict_types=1);

	namespace Edde\Api\Thread;

	use Edde\Api\Config\IConfigurable;

	/**
	 * Thread manager is general service to work with threaded jobs.
	 */
	interface IThreadManager extends IConfigurable {
		/**
		 * execute the thread (should be safe to call arbitrary)
		 *
		 * @return IThreadManager
		 */
		public function execute(): IThreadManager;

		/**
		 * enqueue job to thread
		 *
		 * @param IJob $job
		 * @param bool $autostart queue will also call execute to ensure thread is running
		 *
		 * @return IThreadManager
		 */
		public function queue(IJob $job, bool $autostart = true): IThreadManager;

		/**
		 * long running function to dequeue all current jobs; this should be called in a "thread" intended for that purpose
		 *
		 * @return IThreadManager
		 */
		public function dequeue(): IThreadManager;
	}

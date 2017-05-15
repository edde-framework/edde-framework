<?php
	declare(strict_types=1);

	namespace Edde\Api\Thread;

	use Edde\Api\Config\IConfigurable;

	/**
	 * Thread manager is general service to work with threaded jobs.
	 */
	interface IThreadManager extends IThreadHandler, IConfigurable {
		/**
		 * register a new thread handler
		 *
		 * @param IThreadHandler $threadHandler
		 *
		 * @return IThreadManager
		 */
		public function registerThreadHandler(IThreadHandler $threadHandler): IThreadManager;

		/**
		 * execute the thread (should be safe to be called at any time)
		 *
		 * @return IThreadManager
		 */
		public function execute(): IThreadManager;
	}

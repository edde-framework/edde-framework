<?php
	declare(strict_types=1);

	namespace Edde\Api\Thread;

	use Edde\Api\Config\IConfigurable;

	/**
	 * Simple class which is responsible for holding number of current threads and answering
	 * questions if it is possible to execute an another thread.
	 *
	 * It is kind of integer configuration number which can be simply used to provide way of
	 * maintaining current number of threads.
	 *
	 * That also means when a thread ends up, this number should be updated; thus implementation
	 * must be "thread" safe (there will be concurrent writes and reads in this class).
	 */
	interface IThreadCount extends IConfigurable {
		/**
		 * return number of current threads
		 *
		 * @return int
		 */
		public function getCount(): int;

		/**
		 * simple answer if an another thread could be executed
		 *
		 * @return bool
		 */
		public function canExecute(): bool;

		/**
		 * increase current number of threads
		 *
		 * @return IThreadCount
		 */
		public function increase(): IThreadCount;

		/**
		 * decrease current number of threads
		 *
		 * @return IThreadCount
		 */
		public function decrease(): IThreadCount;

		/**
		 * update thread; save/load thread count to get current number of threads
		 *
		 * @return IThreadCount
		 */
		public function update(): IThreadCount;

		/**
		 * lock or wait to lock; this is useful to create a "transaction" - get number of threads and update
		 *
		 * @return IThreadCount
		 */
		public function lock(): IThreadCount;

		/**
		 * release the lock
		 *
		 * @return IThreadCount
		 */
		public function unlock(): IThreadCount;
	}

<?php
	declare(strict_types=1);

	namespace Edde\Api\Thread;

	interface IThreadHandler {
		/**
		 * execute job dequeuing in thread
		 *
		 * @return IThreadHandler
		 */
		public function dequeue(): IThreadHandler;
	}

<?php
	declare(strict_types=1);

	namespace Edde\Api\Application;

	use Edde\Api\Config\IConfigurable;
	use Iterator;
	use IteratorAggregate;

	/**
	 * Request queue is simple class holding all requests against the application to be processed
	 * in a (single) run.
	 */
	interface IRequestQueue extends IConfigurable, IteratorAggregate {
		/**
		 * queue the given request
		 *
		 * @param IRequest $request
		 *
		 * @return IRequestQueue
		 */
		public function queue(IRequest $request): IRequestQueue;

		/**
		 * are there some requests in the queue?
		 *
		 * @return bool
		 */
		public function isEmpty(): bool;

		/**
		 * @return IRequest[]|Iterator
		 */
		public function getIterator();
	}

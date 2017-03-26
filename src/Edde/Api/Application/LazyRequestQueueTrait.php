<?php
	declare(strict_types=1);

	namespace Edde\Api\Application;

	trait LazyRequestQueueTrait {
		/**
		 * @var IRequestQueue
		 */
		protected $requestQueue;

		/**
		 * @param IRequestQueue $requestQueue
		 */
		public function lazyRequestQueue(IRequestQueue $requestQueue) {
			$this->requestQueue = $requestQueue;
		}
	}

<?php
	declare(strict_types=1);

	namespace Edde\Api\Protocol;

	trait LazyElementQueueTrait {
		/**
		 * @var IElementQueue|IElement[]
		 */
		protected $elementQueue;

		/**
		 * @param IElementQueue $elementQueue
		 */
		public function lazyElementQueue(IElementQueue $elementQueue) {
			$this->elementQueue = $elementQueue;
		}
	}

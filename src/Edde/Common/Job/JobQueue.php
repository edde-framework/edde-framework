<?php
	declare(strict_types=1);

	namespace Edde\Common\Job;

	use Edde\Api\Job\IJobQueue;
	use Edde\Api\Protocol\IElement;
	use Edde\Api\Store\LazyStoreTrait;

	class JobQueue extends AbstractJobQueue {
		use LazyStoreTrait;

		/**
		 * @inheritdoc
		 */
		public function queue(IElement $element): IJobQueue {
			$this->store->append(static::class, $element);
			return $this;
		}

		/**
		 * @inheritdoc
		 */
		public function hasJob(): bool {
			return $this->store->has(static::class);
		}

		/**
		 * @inheritdoc
		 */
		public function dequeue() {
			/** @var $element IElement */
			foreach ($this->store->pickup(static::class, []) as $element) {
				yield $element;
			}
		}
	}

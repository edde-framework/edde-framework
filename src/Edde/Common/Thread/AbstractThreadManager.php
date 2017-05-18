<?php
	declare(strict_types=1);

	namespace Edde\Common\Thread;

	use Edde\Api\Store\LazyStoreTrait;
	use Edde\Api\Thread\IThreadHandler;
	use Edde\Api\Thread\IThreadManager;
	use Edde\Api\Thread\LazyExecutorTtrait;
	use Edde\Common\Config\ConfigurableTrait;

	abstract class AbstractThreadManager extends AbstractThreadHandler implements IThreadManager {
		use LazyExecutorTtrait;
		use LazyStoreTrait;
		use ConfigurableTrait;
		/**
		 * @var IThreadHandler[]
		 */
		protected $threadHandlerList = [];
		/**
		 * @var int
		 */
		protected $maximumThreadCount = 4;

		/**
		 * @inheritdoc
		 */
		public function registerThreadHandler(IThreadHandler $threadHandler): IThreadManager {
			$this->threadHandlerList[] = $threadHandler;
			return $this;
		}

		/**
		 * @inheritdoc
		 */
		public function dequeue(): IThreadHandler {
			foreach ($this->threadHandlerList as $threadHandler) {
				$threadHandler->dequeue();
			}
			return $this;
		}

		/**
		 * @inheritdoc
		 */
		public function hasQueue(): bool {
			foreach ($this->threadHandlerList as $threadHandler) {
				if ($threadHandler->hasQueue()) {
					return true;
				}
			}
			return false;
		}

		/**
		 * @inheritdoc
		 */
		public function execute(): IThreadManager {
			$this->executor->execute();
			return $this;
		}

		/**
		 * @inheritdoc
		 */
		public function pool(): IThreadManager {
			if ($this->hasQueue() === false) {
				return $this;
			}
			$this->updateThreadCount(1);
			try {
				$this->dequeue();
				return $this;
			} finally {
				$this->updateThreadCount(-1);
			}
		}

		protected function updateThreadCount(int $number) {
			$this->store->lock($lock = (static::class . '/currentThreadCount'));
			$this->store->set($lock, $this->store->get($lock, 0) + $number);
			$this->store->unlock($lock);
		}

		/**
		 * @inheritdoc
		 */
		public function setMaximumThreadCount(int $maximumThreadCount): IThreadManager {
			$this->maximumThreadCount = $maximumThreadCount;
			return $this;
		}
	}

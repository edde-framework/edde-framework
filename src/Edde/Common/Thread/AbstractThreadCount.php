<?php
	declare(strict_types=1);

	namespace Edde\Common\Thread;

	use Edde\Api\Store\LazyStoreTrait;
	use Edde\Api\Thread\IThreadCount;
	use Edde\Common\Config\ConfigurableTrait;
	use Edde\Common\Object;

	abstract class AbstractThreadCount extends Object implements IThreadCount {
		use LazyStoreTrait;
		use ConfigurableTrait;
		/**
		 * @var int
		 */
		protected $count = 0;

		/**
		 * @inheritdoc
		 */
		public function getCount(): int {
			return $this->count;
		}

		/**
		 * @inheritdoc
		 */
		public function canExecute(): bool {
			return true;
		}

		/**
		 * @inheritdoc
		 */
		public function increase(): IThreadCount {
			$this->count++;
			return $this;
		}

		/**
		 * @inheritdoc
		 */
		public function decrease(): IThreadCount {
			$this->count--;
			return $this;
		}

		/**
		 * @inheritdoc
		 */
		public function update(): IThreadCount {
			return $this;
		}

		/**
		 * @inheritdoc
		 */
		public function lock(): IThreadCount {
			$this->store->lock(static::class);
			return $this;
		}

		/**
		 * @inheritdoc
		 */
		public function unlock(): IThreadCount {
			$this->store->unlock(static::class);
			return $this;
		}
	}

<?php
	declare(strict_types=1);

	namespace Edde\Common\Thread;

	use Edde\Api\Thread\IThreadCount;
	use Edde\Common\Config\ConfigurableTrait;
	use Edde\Common\Object;

	abstract class AbstractThreadCount extends Object implements IThreadCount {
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
	}

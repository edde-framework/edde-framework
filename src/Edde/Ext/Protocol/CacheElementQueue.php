<?php
	declare(strict_types=1);

	namespace Edde\Ext\Protocol;

	use Edde\Api\Protocol\IElementQueue;
	use Edde\Common\Cache\CacheTrait;
	use Edde\Common\Protocol\AbstractElementQueue;

	class CacheElementQueue extends AbstractElementQueue {
		use CacheTrait;

		/**
		 * @inheritdoc
		 */
		public function save(): IElementQueue {
			$this->cache()->save('queue', [
				$this->queueList,
				$this->elementList,
			]);
			return $this;
		}

		/**
		 * @inheritdoc
		 */
		public function load(): IElementQueue {
			list($this->queueList, $this->elementList) = $this->cache()->load('queue', [
				[],
				[],
			]);
			return $this;
		}
	}

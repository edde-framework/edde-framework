<?php
	declare(strict_types=1);

	namespace Edde\Ext\Protocol;

	use Edde\Api\Protocol\IElementQueue;
	use Edde\Api\Store\LazyStoreTrait;
	use Edde\Common\Protocol\AbstractElementQueue;

	class ElementQueue extends AbstractElementQueue {
		use LazyStoreTrait;

		/**
		 * @inheritdoc
		 */
		public function save(): IElementQueue {
			$this->store->block($lock = static::class . '/element-queue');
			$queueList = $this->queueList;
			$elementList = $this->elementList;
			$this->load();
			$this->store->set($lock, [
				array_filter(array_merge($this->queueList, $queueList)),
				array_filter(array_merge($this->elementList, $elementList)),
			]);
			$this->store->unlock($lock);
			return $this;
		}

		/**
		 * @inheritdoc
		 */
		public function load(): IElementQueue {
			list($this->queueList, $this->elementList) = $this->store->get(static::class . '/element-queue', [
				[],
				[],
			]);
			return $this;
		}
	}

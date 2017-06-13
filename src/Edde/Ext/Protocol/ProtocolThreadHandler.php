<?php
	declare(strict_types=1);

	namespace Edde\Ext\Protocol;

	use Edde\Api\Protocol\LazyElementQueueTrait;
	use Edde\Api\Protocol\LazyProtocolServiceTrait;
	use Edde\Api\Thread\IThreadHandler;
	use Edde\Common\Thread\AbstractThreadHandler;

	class ProtocolThreadHandler extends AbstractThreadHandler {
		use LazyProtocolServiceTrait;
		use LazyElementQueueTrait;

		/**
		 * @inheritdoc
		 */
		public function dequeue(): IThreadHandler {
			$this->elementQueue->load();
			$this->protocolService->dequeue();
			$this->elementQueue->save();
			return $this;
		}

		/**
		 * @inheritdoc
		 */
		public function hasQueue(): bool {
			$this->elementQueue->load();
			return $this->elementQueue->isEmpty() === false;
		}
	}

<?php
	declare(strict_types=1);

	namespace Edde\Ext\Protocol;

	use Edde\Api\Protocol\LazyElementQueueTrait;
	use Edde\Api\Thread\IThreadHandler;
	use Edde\Common\Thread\AbstractThreadHandler;

	class ProtocolThreadHandler extends AbstractThreadHandler {
		use LazyElementQueueTrait;

		/**
		 * @inheritdoc
		 */
		public function dequeue(): IThreadHandler {
			$this->elementQueue->load();
			$this->elementQueue->execute();
			$this->elementQueue->save();
			return $this;
		}
	}

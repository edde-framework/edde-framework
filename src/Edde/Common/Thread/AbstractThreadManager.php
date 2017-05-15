<?php
	declare(strict_types=1);

	namespace Edde\Common\Thread;

	use Edde\Api\Thread\IThreadHandler;
	use Edde\Api\Thread\IThreadManager;
	use Edde\Api\Thread\LazyExecutorTtrait;
	use Edde\Common\Config\ConfigurableTrait;

	abstract class AbstractThreadManager extends AbstractThreadHandler implements IThreadManager {
		use LazyExecutorTtrait;
		use ConfigurableTrait;
		/**
		 * @var IThreadHandler[]
		 */
		protected $threadHandlerList = [];

		/**
		 * @inheritdoc
		 */
		public function registerThreadHandler(IThreadHandler $threadHandler): IThreadManager {
			$this->threadHandlerList[] = $threadHandler;
			return $this;
		}

		/**
		 * @return IThreadHandler
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
		public function execute(): IThreadManager {
			$this->executor->execute();
			return $this;
		}
	}

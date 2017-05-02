<?php
	declare(strict_types=1);

	namespace Edde\Common\Thread;

	use Edde\Api\Thread\IJob;
	use Edde\Api\Thread\IThreadManager;
	use Edde\Api\Thread\LazyExecutorTtrait;
	use Edde\Common\Config\ConfigurableTrait;
	use Edde\Common\Object;

	abstract class AbstractThreadManager extends Object implements IThreadManager {
		use LazyExecutorTtrait;
		use ConfigurableTrait;

		/**
		 * @inheritdoc
		 */
		public function execute(): IThreadManager {
			$this->executor->setup();
			$this->executor->execute();
			return $this;
		}

		/**
		 * @inheritdoc
		 */
		public function queue(IJob $job, bool $autostart = true): IThreadManager {
			return $this;
		}

		/**
		 * @inheritdoc
		 */
		public function dequeue(): IThreadManager {
			return $this;
		}
	}

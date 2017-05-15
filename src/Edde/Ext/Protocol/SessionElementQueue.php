<?php
	declare(strict_types=1);

	namespace Edde\Ext\Protocol;

	use Edde\Api\Protocol\IElementQueue;
	use Edde\Common\Protocol\AbstractElementQueue;
	use Edde\Common\Session\SessionTrait;

	class SessionElementQueue extends AbstractElementQueue {
		use SessionTrait;

		/**
		 * @inheritdoc
		 */
		public function save(): IElementQueue {
			$this->session()->set('queue', [
				$this->queueList,
				$this->elementList,
			]);
			return $this;
		}

		/**
		 * @inheritdoc
		 */
		public function load(): IElementQueue {
			list($this->queueList, $this->elementList) = $this->session()->get('queue', [
				[],
				[],
			]);
			return $this;
		}
	}

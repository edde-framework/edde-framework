<?php
	declare(strict_types=1);

	namespace Edde\Common\Protocol;

	use Edde\Api\Protocol\IEvent;

	class Event extends AbstractElement implements IEvent {
		/**
		 * @var bool
		 */
		protected $cancel;

		public function __construct() {
			parent::__construct('event');
			$this->cancel = false;
		}

		/**
		 * @inheritdoc
		 */
		public function cancel(bool $cancel = true): IEvent {
			$this->cancel = $cancel;
			return $this;
		}

		/**
		 * @inheritdoc
		 */
		public function isCanceled(): bool {
			return $this->cancel;
		}
	}

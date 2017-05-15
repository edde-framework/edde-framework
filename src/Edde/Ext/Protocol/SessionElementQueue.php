<?php
	declare(strict_types=1);

	namespace Edde\Ext\Protocol;

	use Edde\Api\Protocol\IElementQueue;
	use Edde\Common\Protocol\AbstractElementQueue;

	class SessionElementQueue extends AbstractElementQueue {
		/**
		 * @inheritdoc
		 */
		public function save(): IElementQueue {
			return $this;
		}

		/**
		 * @inheritdoc
		 */
		public function load(): IElementQueue {
			return $this;
		}
	}

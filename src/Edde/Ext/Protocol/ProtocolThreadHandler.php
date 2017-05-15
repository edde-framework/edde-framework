<?php
	declare(strict_types=1);

	namespace Edde\Ext\Protocol;

	use Edde\Api\Thread\IThreadHandler;
	use Edde\Common\Thread\AbstractThreadHandler;

	class ProtocolThreadHandler extends AbstractThreadHandler {
		/**
		 * @inheritdoc
		 */
		public function dequeue(): IThreadHandler {
			return $this;
		}
	}

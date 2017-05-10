<?php
	declare(strict_types=1);

	namespace Edde\Common\Protocol\Request;

	use Edde\Api\Protocol\Request\IMessage;
	use Edde\Common\Protocol\AbstractElement;

	class Message extends AbstractElement implements IMessage {
		/**
		 * @var string
		 */
		protected $request;

		public function __construct(string $request = null) {
			parent::__construct('message');
			$this->request = $request;
		}

		/**
		 * @inheritdoc
		 */
		public function getRequest(): string {
			return $this->request;
		}

		/**
		 * @inheritdoc
		 */
		public function packet(): \stdClass {
			$packet = parent::packet();
			$packet->request = $this->getRequest();
			return $packet;
		}

		public function __clone() {
			parent::__clone();
			$this->request = null;
		}
	}

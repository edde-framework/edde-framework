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

		public function __construct(string $request, string $type = null, string $id = null) {
			parent::__construct($type ?: 'message', $id);
			$this->request = $request;
		}

		/**
		 * @inheritdoc
		 */
		public function getRequest(): string {
			return $this->request;
		}
	}

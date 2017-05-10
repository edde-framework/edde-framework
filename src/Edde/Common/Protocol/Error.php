<?php
	declare(strict_types=1);

	namespace Edde\Common\Protocol;

	use Edde\Api\Protocol\IError;

	class Error extends AbstractElement implements IError {
		/**
		 * @var int
		 */
		protected $code;
		/**
		 * @var string
		 */
		protected $message;
		protected $exception;

		public function __construct(int $code, string $message) {
			parent::__construct('error');
			$this->code = $code;
			$this->message = $message;
		}

		/**
		 * @inheritdoc
		 */
		public function getCode(): int {
			return $this->code;
		}

		/**
		 * @inheritdoc
		 */
		public function getMessage(): string {
			return $this->message;
		}

		public function setException(string $exception): IError {
			$this->exception = $exception;
			return $this;
		}

		/**
		 * @inheritdoc
		 */
		public function getException() {
			return $this->exception;
		}

		/**
		 * @inheritdoc
		 */
		public function getStack(): array {
			return [];
		}

		public function packet(): \stdClass {
			$packet = parent::packet();
			$packet->code = $this->code;
			$packet->message = $this->message;
			if ($this->exception) {
				$packet->exception = $this->exception;
			}
			return $packet;
		}
	}

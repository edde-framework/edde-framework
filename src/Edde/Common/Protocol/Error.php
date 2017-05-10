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
		/**
		 * @var string
		 */
		protected $exception;

		public function __construct() {
			parent::__construct('error');
		}

		/**
		 * @inheritdoc
		 */
		public function setCode(int $code): IError {
			$this->code = $code;
			return $this;
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
		public function setMessage(string $message): IError {
			$this->message = $message;
			return $this;
		}

		/**
		 * @inheritdoc
		 */
		public function getMessage(): string {
			return $this->message;
		}

		/**
		 * @inheritdoc
		 */
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

		public function __clone() {
			parent::__clone();
			$this->code = null;
			$this->message = null;
			$this->exception = null;
		}
	}

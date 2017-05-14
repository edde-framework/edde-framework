<?php
	declare(strict_types=1);

	namespace Edde\Common\Protocol;

	class Error extends Element {
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

		public function __construct(int $code = 0, string $message = null) {
			parent::__construct('error', null, [
				'code'    => $code,
				'message' => $message,
			]);
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

		/**
		 * @inheritdoc
		 */
		public function setException(string $exception) {
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
	}

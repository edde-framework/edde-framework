<?php
	declare(strict_types=1);

	namespace Edde\Common\Protocol;

	use Edde\Api\Protocol\IElement;
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
		 * @var IElement
		 */
		protected $element;
		protected $exception;

		public function __construct(int $code, string $message, IElement $element = null) {
			parent::__construct('error');
			$this->code = $code;
			$this->message = $message;
			$this->element = $element;
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
		public function getElement() {
			return $this->element;
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
			if ($this->element) {
				$packet->element = $this->element->getId();
			}
			if ($this->exception) {
				$packet->exception = $this->exception;
			}
			return $packet;
		}
	}

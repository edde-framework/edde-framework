<?php
	declare(strict_types=1);

	namespace Edde\Common\Protocol;

	use Edde\Api\Protocol\IElement;
	use Edde\Api\Protocol\IError;

	class Error extends AbstractElement implements IError {
		/**
		 * reference to the failed element
		 *
		 * @var IElement
		 */
		protected $element;
		/**
		 * @var int
		 */
		protected $code;
		/**
		 * @var string
		 */
		protected $message;
		protected $exception;

		public function __construct(IElement $element, int $code, string $message) {
			parent::__construct('error');
			$this->element = $element;
			$this->code = $code;
			$this->message = $message;
		}

		/**
		 * @inheritdoc
		 */
		public function getElement(): IElement {
			return $this->element;
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
	}

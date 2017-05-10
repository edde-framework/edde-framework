<?php
	declare(strict_types=1);

	namespace Edde\Api\Protocol;

	interface IError extends IElement {
		/**
		 * @param int $code
		 *
		 * @return IError
		 */
		public function setCode(int $code): IError;

		/**
		 * return code which should be unique for the error
		 *
		 * @return int
		 */
		public function getCode(): int;

		/**
		 * @param string $message
		 *
		 * @return IError
		 */
		public function setMessage(string $message): IError;

		/**
		 * return human readable message
		 *
		 * @return string
		 */
		public function getMessage(): string;

		/**
		 * @param string $exception
		 *
		 * @return IError
		 */
		public function setException(string $exception): IError;

		/**
		 * return exception class or null
		 *
		 * @return string|null
		 */
		public function getException();

		/**
		 * optional fail stack
		 *
		 * @return string[]
		 */
		public function getStack(): array;
	}

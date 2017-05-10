<?php
	declare(strict_types=1);

	namespace Edde\Api\Protocol;

	interface IError extends IElement {
		/**
		 * return code which should be unique for the error
		 *
		 * @return int
		 */
		public function getCode(): int;

		/**
		 * return human readable message
		 *
		 * @return string
		 */
		public function getMessage(): string;

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

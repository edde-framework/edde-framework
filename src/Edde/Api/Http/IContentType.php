<?php
	declare(strict_types = 1);

	namespace Edde\Api\Http;

	/**
	 * Formal content type implementation; content type can have additional parameters (thus extended from IList).
	 */
	interface IContentType {
		/**
		 * return mime type of this content type
		 *
		 * @return string
		 */
		public function getMime(): string;

		/**
		 * return charset parameter of this mime type
		 *
		 * @param string $default
		 *
		 * @return string
		 */
		public function getCharset(string $default = 'utf-8'): string;

		/**
		 * return mime type of this content type
		 *
		 * @return string
		 */
		public function __toString(): string;
	}

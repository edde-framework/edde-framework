<?php
	declare(strict_types = 1);

	namespace Edde\Api\Http;

	interface IBody {
		/**
		 * return the original body of a request
		 *
		 * @return string
		 */
		public function getBody();

		/**
		 * @return string
		 */
		public function getMime(): string;

		/**
		 * @return string
		 */
		public function getTarget(): string;

		/**
		 * try to convert a request body to specified target using system-wide converter manager
		 *
		 * @param string $target
		 *
		 * @param string $mime override the original mime type for convesion
		 *
		 * @return mixed
		 */
		public function convert(string $target = null, string $mime = null);
	}

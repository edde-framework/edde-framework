<?php
	declare(strict_types = 1);

	namespace Edde\Api\Http;

	interface IBody {
		/**
		 * return the original body of a request
		 *
		 * @return string
		 */
		public function getBody(): string;

		/**
		 * try to convert a request body to specified target using system-wide converter manager
		 *
		 * @param string $target
		 *
		 * @return mixed
		 */
		public function convert(string $target);
	}

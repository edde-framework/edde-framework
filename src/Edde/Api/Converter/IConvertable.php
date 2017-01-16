<?php
	declare(strict_types=1);

	namespace Edde\Api\Converter;

	interface IConvertable {
		/**
		 * return target mime type
		 *
		 * @return string
		 */
		public function getTarget(): string;

		/**
		 * try to convert an input
		 *
		 * @return mixed
		 */
		public function convert();
	}

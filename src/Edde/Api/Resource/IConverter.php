<?php
	declare(strict_types = 1);

	namespace Edde\Api\Resource;

	/**
	 * Support for a generic type conversion.
	 */
	interface IConverter {
		/**
		 * formally supported input mime type
		 *
		 * @return string
		 */
		public function getMime(): string;

		/**
		 * convert input type to a output defined by a target; same target however can have more output types (for example string, node, ...)
		 *
		 * @param mixed $source
		 * @param string $target target mime (general identifier); converter should throw an exception if a target is unknown/unsuporrted
		 *
		 * @return mixed
		 */
		public function convert($source, string $target);
	}

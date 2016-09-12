<?php
	declare(strict_types = 1);

	namespace Edde\Api\Converter;

	/**
	 * Support for a generic type conversion.
	 */
	interface IConverter {
		/**
		 * get list of supported mime types (or generic identifiers); they should be used only as alias (for example application/json, text/json, ...) and not for
		 * logical differenciating of types; in other words - all mime list must be compatible with all (internally supported) targets (not only combinations)
		 *
		 * @return array
		 */
		public function getMimeList(): array;

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
<?php
	declare(strict_types = 1);

	namespace Edde\Api\Converter;

	use Edde\Api\Usable\IDeffered;

	interface IConverterManager extends IDeffered {
		/**
		 * register a converter
		 *
		 * @param IConverter $converter
		 *
		 * @param bool $force
		 *
		 * @return IConverterManager
		 */
		public function registerConverter(IConverter $converter, bool $force = false): IConverterManager;

		/**
		 * magical method for generic data conversion; ideologically it is based on a mime type conversion, but identifiers can be arbitrary
		 *
		 * @param mixed $source generic input which will be converted in a generic output (defined by mime a target)
		 * @param string $mime generic identifier, it can be formal mime type or anything else (but there must be known converter)
		 * @param string $target target type of conversion
		 *
		 * @return mixed return converted source; result depends on mime+target combination
		 */
		public function convert($source, string $mime, string $target);
	}

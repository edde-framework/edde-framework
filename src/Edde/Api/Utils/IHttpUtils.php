<?php
	declare(strict_types=1);

	namespace Edde\Api\Utils;

	use Edde\Api\Config\IConfigurable;

	interface IHttpUtils extends IConfigurable {
		/**
		 * parse an accept header; should return ordered list of accepted mime types
		 *
		 * @param string|null $accept
		 *
		 * @return array
		 */
		public function accept(string $accept = null): array;
	}

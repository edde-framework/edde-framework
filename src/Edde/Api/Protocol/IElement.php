<?php
	declare(strict_types=1);

	namespace Edde\Api\Protocol;

	interface IElement {
		/**
		 * return simple string name of the element name
		 *
		 * @return string
		 */
		public function getType(): string;
	}

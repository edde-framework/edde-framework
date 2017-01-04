<?php
	declare(strict_types = 1);

	namespace Edde\Api\Serialize;

	interface ISerializable {
		/**
		 * generate object has to be able to determinate of object has already been created
		 *
		 * @return string
		 */
		public function hash(): string;
	}

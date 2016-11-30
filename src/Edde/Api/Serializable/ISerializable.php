<?php
	declare(strict_types = 1);

	namespace Edde\Api\Serializable;

	/**
	 * Formal interface for serializable object.
	 */
	interface ISerializable {
		/**
		 * wake up an object
		 *
		 * @return ISerializable
		 */
		public function warmup(): ISerializable;

		/**
		 * return serialized form of an object
		 *
		 * @return string
		 */
		public function serialize(): string;
	}

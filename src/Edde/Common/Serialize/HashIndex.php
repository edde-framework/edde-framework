<?php
	declare(strict_types = 1);

	namespace Edde\Common\Serialize;

	use Edde\Api\Serialize\IHashable;
	use Edde\Api\Serialize\ISerializable;
	use Edde\Api\Serialize\SerializeException;
	use Edde\Common\Object;

	/**
	 * Index of serialized objects.
	 */
	class HashIndex extends Object {
		/**
		 * @var ISerializable[]
		 */
		static protected $index = [];

		static public function save(ISerializable $serializable, bool $force = false) {
			if (($serializable instanceof IHashable && isset(self::$index[$hash = $serializable->hash()]) === false) || $force === true) {
				/** @noinspection PhpUndefinedVariableInspection */
				self::$index[$hash] = $serializable;
			}
		}

		static public function load(string $hash): ISerializable {
			if (isset(self::$index[$hash]) === false) {
				throw new SerializeException(sprintf('Unknown object hash [%s]; object is not present in hash index.', $hash));
			}
			return self::$index[$hash];
		}

		/**
		 * clear current object index
		 */
		static public function drop() {
			self::$index = [];
		}

		/**
		 * @return ISerializable[]
		 */
		static public function getIndex(): array {
			return self::$index;
		}

		/**
		 * return serialized hash index
		 *
		 * @return string
		 */
		static public function serialize(): string {
			return serialize(self::$index);
		}

		/**
		 * restore (override current hash index) serialized index
		 *
		 * @param string $index
		 */
		static public function unserialize(string $index) {
			self::$index = unserialize($index);
		}
	}

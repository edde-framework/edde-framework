<?php
	declare(strict_types = 1);

	namespace Edde\Api\Crypt;

	use Edde\Api\Usable\IUsable;

	/**
	 * Interface for encapsulating encryption, decryption and other related stuff.
	 */
	interface ICryptEngine extends IUsable {
		/**
		 * generate stream of bytes in given length
		 *
		 * @param int $length
		 *
		 * @return string
		 */
		public function bytes(int $length): string;

		/**
		 * generate random string with given character set (e.g. password)
		 *
		 * @param int $length
		 * @param string $charlist
		 *
		 * @return string
		 */
		public function generate(int $length = 10, string $charlist = '0-9a-z'): string;

		/**
		 * generate standard GUID
		 *
		 * @param string|null $seed
		 *
		 * @return string
		 */
		public function guid(string $seed = null): string;
	}
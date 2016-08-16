<?php
	declare(strict_types = 1);

	namespace Edde\Common\Crypt;

	use Edde\Api\Crypt\CryptException;
	use Edde\Api\Crypt\ICryptEngine;
	use Edde\Common\Usable\AbstractUsable;

	class CryptEngine extends AbstractUsable implements ICryptEngine {
		public function generate(int $length = 10, string $charlist = '0-9a-z'): string {
			$this->use();
			$charlist = str_shuffle(preg_replace_callback('#.-.#', function ($m) {
				return implode('', range($m[0][0], $m[0][2]));
			}, $charlist));
			$charlistLength = strlen($charlist);
			$string = '';
			$rand0 = null;
			$rand1 = null;
			$rand2 = $this->bytes($length);
			for ($i = 0; $i < $length; $i++) {
				if ($i % 5 === 0) {
					list($rand0, $rand1) = explode(' ', microtime());
					$rand0 += lcg_value();
				}
				$rand0 *= $charlistLength;
				$string .= $charlist[($rand0 + $rand1 + ord($rand2[$i % strlen($rand2)])) % $charlistLength];
				$rand0 -= (int)$rand0;
			}
			return $string;
		}

		public function bytes(int $length): string {
			$this->use();
			if (function_exists('random_bytes')) {
				return random_bytes($length);
			} else if (function_exists('openssl_random_pseudo_bytes')) {
				return openssl_random_pseudo_bytes($length);
			} else if (function_exists('mcrypt_create_iv')) {
				return mcrypt_create_iv($length, MCRYPT_DEV_URANDOM);
			} else if (@is_readable('/dev/urandom')) {
				return file_get_contents('/dev/urandom', false, null, -1, $length);
			}
			throw new CryptException('There is no available source of random numbers!');
		}

		public function guid(string $seed = null): string {
			$this->use();
			$data = $seed ? substr(hash('sha512', $seed), 0, 16) : $this->bytes(16);
			$data[6] = chr(ord($data[6]) & 0x0f | 0x40);
			$data[8] = chr(ord($data[8]) & 0x3f | 0x80);
			return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
		}

		protected function prepare() {
		}
	}

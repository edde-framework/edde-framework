<?php
	namespace Edde\Common\Strings;

	use Edde\Common\AbstractObject;
	use Edde\Common\Callback\CallbackUtils;

	/**
	 * StringsUtils are set of independent methods for UTF-8 string manipulation.
	 */
	class StringUtils extends AbstractObject {
		private static $SEPARATOR_LIST = [
			'|',
			':',
			'.',
			'-',
			'_',
			'/',
		];

		/**
		 * try to capitalize input string
		 *
		 * @param string $string
		 *
		 * @return string
		 */
		static public function capitalize($string) {
			return mb_convert_case($string, MB_CASE_TITLE, 'UTF-8');
		}

		/**
		 * compare two strings
		 *
		 * @param string $left
		 * @param string $right
		 * @param int|null $length
		 *
		 * @return bool
		 */
		static public function compare($left, $right, $length = null) {
			if ($length !== null) {
				$left = self::substring($left, 0, $length);
				$right = self::substring($right, 0, $length);
			}
			return self::lower($left) === self::lower($right);
		}

		/**
		 * return subset of the given string
		 *
		 * @param string $string
		 * @param int $start
		 * @param int|null $length
		 *
		 * @return string
		 */
		static public function substring($string, $start, $length = null) {
			return mb_substr($string, $start, $length, 'UTF-8');
		}

		/**
		 * @param string $string
		 *
		 * @return string
		 */
		static public function lower($string) {
			return mb_strtolower($string, 'UTF-8');
		}

		/**
		 * ensure that input string is unicode; if not exception is thrown
		 *
		 * @param string $string
		 *
		 * @return string
		 * @throws StringException
		 */
		static public function checkUnicode($string) {
			if (strpos($string, "\xEF\xBB\xBF", 0) !== false) {
				$string = substr($string, 3);
			}
			if (preg_match('~~u', $string) !== 1) {
				throw new StringException('String is not valid UTF-8 stream.');
			}
			return str_replace("\r\n", "\n", $string);
		}

		/**
		 * @param int $code
		 *
		 * @return string
		 * @throws StringException
		 */
		static public function chr($code) {
			if ($code < 0 || ($code >= 0xD800 && $code <= 0xDFFF) || $code > 0x10FFFF) {
				throw new StringException('Code point must be in range 0x0 to 0xD7FF or 0xE000 to 0x10FFFF.');
			}
			return iconv('UTF-32BE', 'UTF-8//IGNORE', pack('N', $code));
		}

		/**
		 * return true if the given string is utf-8
		 *
		 * @param string $string
		 *
		 * @return bool
		 */
		static public function isEncoding($string) {
			return $string === self::fixEncoding($string);
		}

		/**
		 * convert given string into utf-8
		 *
		 * @param string $string
		 *
		 * @return string
		 */
		static public function fixEncoding($string) {
			return htmlspecialchars_decode(htmlspecialchars($string, ENT_NOQUOTES | ENT_IGNORE, 'UTF-8'), ENT_NOQUOTES);
		}

		/**
		 * translate given size to the human readable form
		 *
		 * @param int $size
		 * @param int $decimal
		 *
		 * @return string
		 */
		static public function toHumanSize($size, $decimal = 2) {
			static $sizeList = [
				'B',
				'kB',
				'MB',
				'GB',
				'TB',
				'PB',
				'EB',
				'ZB',
				'YB',
			];
			$factor = (int)floor((strlen($size) - 1) / 3);
			return sprintf('%.' . $decimal . 'f %s', $size / pow(1024, $factor), @$sizeList[$factor]);
		}

		/**
		 * split given string by capital letters and glue them by the given glue - e.g. FooBar will became foo-bar
		 *
		 * @param string $string
		 * @param string $glue
		 * @param int $index
		 *
		 * @return string
		 */
		static public function recamel($string, $glue = '-', $index = 0) {
			$camel = self::camel($string, $index);
			return mb_strtolower(implode($glue, $camel));
		}

		/**
		 * split the given string by capital letters (e.g. FooBar will became [Foo, Bar])
		 *
		 * @param string $string
		 * @param int $index
		 *
		 * @return array
		 */
		static public function camel($string, $index = 0) {
			$camel = preg_split('~(?=[A-Z])~', $string, -1, PREG_SPLIT_NO_EMPTY);
			if ($index > 0) {
				return array_slice($camel, $index);
			}
			return $camel;
		}

		/**
		 * oposite of {@see self::recamel()} method; convert foo-bar to the FooBar form
		 *
		 * @param string $string
		 * @param string|null $separator
		 *
		 * @return string
		 */
		static public function camelize($string, $separator = null) {
			return str_replace('~', null, mb_convert_case(str_replace($separator ?: self::$SEPARATOR_LIST, '~', mb_strtolower(implode('~', preg_split('~(?=[A-Z])~', $string, -1, PREG_SPLIT_NO_EMPTY)))), MB_CASE_TITLE, 'UTF-8'));
		}

		static public function webalize($string, $charlist = null, $lower = true) {
			$string = self::toAscii($string);
			if ($lower) {
				$string = strtolower($string);
			}
			$string = preg_replace('~[^a-z0-9' . preg_quote($charlist, '~') . ']+~i', '-', $string);
			$string = trim($string, '-');
			return $string;
		}

		static public function toAscii($string) {
			$string = preg_replace('~[^\x09\x0A\x0D\x20-\x7E\xA0-\x{2FF}\x{370}-\x{10FFFF}]~u', '', $string);
			$string = strtr($string, '`\'"^~?', "\x01\x02\x03\x04\x05\x06");
			$string = str_replace([
				"\xE2\x80\x9E",
				"\xE2\x80\x9C",
				"\xE2\x80\x9D",
				"\xE2\x80\x9A",
				"\xE2\x80\x98",
				"\xE2\x80\x99",
				"\xC2\xB0",
			], [
				"\x03",
				"\x03",
				"\x03",
				"\x02",
				"\x02",
				"\x02",
				"\x04",
			], $string);
			$string = @iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $string); // intentionally @
			$string = str_replace([
				'`',
				"'",
				'"',
				'^',
				'~',
				'?',
			], '', $string);
			return strtr($string, "\x01\x02\x03\x04\x05\x06", '`\'"^~?');
		}

		/**
		 * preg_match
		 *
		 * @param string $string
		 * @param string $pattern
		 * @param bool|false $named return only named parameters from token
		 * @param array|bool|false $trim if array is provided, its used for named parameters defaults
		 *
		 * @return array|null
		 */
		static public function match($string, $pattern, $named = false, $trim = false) {
			$match = null;
			$match = self::pcre('preg_match', [
				$pattern,
				$string,
				&$match,
			]) ? $match : null;
			if ($match === null) {
				return null;
			}
			if ($named) {
				foreach ($match as $k => $v) {
					if (is_int(($k)) || ((is_array($trim) || $trim) && empty($v))) {
						unset($match[$k]);
					}
				}
			}
			if (is_array($trim)) {
				$match = array_merge($trim, $match);
			}
			return $match;
		}

		static private function pcre($func, $args) {
			static $messages = [
				PREG_INTERNAL_ERROR => 'Internal error',
				PREG_BACKTRACK_LIMIT_ERROR => 'Backtrack limit was exhausted',
				PREG_RECURSION_LIMIT_ERROR => 'Recursion limit was exhausted',
				PREG_BAD_UTF8_ERROR => 'Malformed UTF-8 data',
				5 => 'Offset didn\'t correspond to the begin of a valid UTF-8 code point',
				// PREG_BAD_UTF8_OFFSET_ERROR
			];
			$res = CallbackUtils::invoke($func, $args, function ($message) use ($args) {
				// compile-time error, not detectable by preg_last_error
				throw new StringException($message . ' in pattern: ' . implode(' or ', (array)$args[0]));
			});
			if (($code = preg_last_error()) // run-time error, but preg_last_error & return code are liars
				&& ($res === null || in_array($func, [
						'preg_filter',
						'preg_replace_callback',
						'preg_replace',
					], true) === false)
			) {
				throw new StringException((isset($messages[$code]) ? $messages[$code] : 'Unknown error') . ' (pattern: ' . implode(' or ', (array)$args[0]) . ')', $code);
			}
			return $res;
		}

		/**
		 * preg_replace
		 *
		 * @param string $subject
		 * @param string $pattern
		 * @param string|null $replacement
		 * @param int $limit
		 *
		 * @return string
		 * @throws StringException
		 */
		static public function replace($subject, $pattern, $replacement = null, $limit = -1) {
			return self::pcre('preg_replace', [
				$pattern,
				$replacement,
				$subject,
				$limit,
			]);
		}

		/**
		 * break down the given string by the given pattern
		 *
		 * @param string $subject
		 * @param string $pattern
		 * @param int $flags
		 *
		 * @return array
		 * @throws StringException
		 */
		static public function split($subject, $pattern, $flags = 0) {
			return self::pcre('preg_split', [
				$pattern,
				$subject,
				-1,
				$flags | PREG_SPLIT_DELIM_CAPTURE,
			]);
		}

		/**
		 * convert first character to upper case
		 *
		 * @param string $string
		 *
		 * @return string
		 */
		static public function firstUpper($string) {
			return self::upper(self::substring($string, 0, 1)) . self::substring($string, 1);
		}

		/**
		 * convert to upper case
		 *
		 * @param string $string
		 *
		 * @return string
		 */
		static public function upper($string) {
			return mb_strtoupper($string, 'UTF-8');
		}

		/**
		 * convert first character to lower case
		 *
		 * @param string $string
		 *
		 * @return string
		 */
		static public function firstLower($string) {
			return self::lower(self::substring($string, 0, 1)) . self::substring($string, 1);
		}

		/**
		 * create an iterator over the given string
		 *
		 * @param string $string
		 *
		 * @return \Generator
		 */
		static public function createIterator($string) {
			$strlen = mb_strlen(self::normalize($string));
			while ($strlen) {
				yield mb_substr($string, 0, 1);
				$string = mb_substr($string, 1, $strlen);
				$strlen = mb_strlen($string);
			}
			yield null;
		}

		/**
		 * normalize input string, trim newlines, ...
		 *
		 * @param string $string
		 *
		 * @return string
		 */
		static public function normalize($string) {
			$string = self::normalizeNewLines($string);
			$string = preg_replace('~[\x00-\x08\x0B-\x1F\x7F-\x9F]+~u', '', $string);
			$string = preg_replace('~[\t ]+$~m', '', $string);
			$string = trim($string, "\n");
			return $string;
		}

		/**
		 * translate an iconsistent newlines to the standard "\n"
		 *
		 * @param string $string
		 *
		 * @return string
		 */
		static public function normalizeNewLines($string) {
			return str_replace([
				"\r\n",
				"\r",
			], "\n", $string);
		}
	}

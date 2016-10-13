<?php
	declare(strict_types = 1);

	namespace Edde\Common\File;

	use Edde\Api\File\FileException;
	use Edde\Api\Url\IUrl;
	use Edde\Common\AbstractObject;
	use Edde\Common\Url\Url;

	class FileUtils extends AbstractObject {
		static protected $mimeTypeList = [
			'xml' => 'text/xml',
			'json' => 'application/json',
			'csv' => 'text/csv',
			'php' => 'text/x-php',
		];

		/**
		 * convert size to human readable size
		 *
		 * @param int $size
		 * @param int $decimals
		 *
		 * @return string
		 */
		static public function humanSize(int $size, int $decimals = 2): string {
			$sizeList = 'BKMGTP';
			$factor = floor((strlen((string)$size) - 1) / 3);
			/** @noinspection PhpUsageOfSilenceOperatorInspection */
			return sprintf("%.{$decimals}f", $size / pow(1024, $factor)) . @$sizeList[(int)$factor];
		}

		/**
		 * return mime type of the given file; this method is a bit more clever
		 *
		 * @param string $file
		 *
		 * @return string
		 * @throws FileException
		 */
		static public function mime(string $file) {
			if (is_file($file) === false) {
				throw new FileException(sprintf('The given file [%s] is not a file.', $file));
			}
			$url = Url::create($file);
			if (isset(self::$mimeTypeList[$type = $url->getExtension()])) {
				return self::$mimeTypeList[$type];
			}
			/** @noinspection PhpUsageOfSilenceOperatorInspection */
			$info = @getimagesize($file); // @ - files smaller than 12 bytes causes read error
			if (isset($info['mime'])) {
				return $info['mime'];
			} else if (extension_loaded('fileinfo')) {
				$type = preg_replace('#[\s;].*\z#', '', finfo_file(finfo_open(FILEINFO_MIME), $file));
			} else if (function_exists('mime_content_type')) {
				$type = mime_content_type($file);
			}
			return isset($type) && preg_match('#^\S+/\S+\z#', $type) ? $type : 'application/octet-stream';
		}

		/**
		 * generate temporary file name; it uses system temp dir (sys_get_temp_dir())
		 *
		 * @param string|null $prefix
		 *
		 * @return string
		 */
		static public function generateTempName($prefix = null) {
			return tempnam(sys_get_temp_dir(), $prefix);
		}

		/**
		 * recreate the given directory with respect to preserve permissions of a given folder
		 *
		 * @param string $path
		 * @param int|null $permissions
		 *
		 * @throws FileException
		 */
		static public function recreate($path, $permissions = null) {
			if ($permissions === null) {
				$permissions = 0777;
				if (file_exists($path)) {
					$permissions = self::getPermission($path);
				}
			}
			self::delete($path);
			self::createDir($path, $permissions);
		}

		/**
		 * return path's permissions
		 *
		 * @param string $path
		 *
		 * @return int
		 */
		static public function getPermission($path) {
			clearstatcache(true, $path);
			return octdec(substr(decoct(fileperms($path)), 1));
		}

		/**
		 * deletes a file or directory
		 *
		 * @param string $path
		 *
		 * @throws FileException
		 */
		static public function delete($path) {
			if (is_file($path) || is_link($path)) {
				$func = DIRECTORY_SEPARATOR === '\\' && is_dir($path) ? 'rmdir' : 'unlink';
				/** @noinspection PhpUsageOfSilenceOperatorInspection */
				if (@$func($path) === false) {
					throw new FileException("Unable to delete [$path].");
				}
			} else if (is_dir($path)) {
				foreach (new \FilesystemIterator($path) as $item) {
					static::delete($item->getRealPath());
				}
				/** @noinspection PhpUsageOfSilenceOperatorInspection */
				if (@rmdir($path) === false) {
					throw new FileException("Unable to delete directory [$path].");
				}
			}
		}

		/**
		 * creates a directory
		 *
		 * @param string $dir
		 * @param int $mode
		 *
		 * @throws FileException
		 */
		static public function createDir($dir, $mode = 0777) {
			/** @noinspection PhpUsageOfSilenceOperatorInspection */
			if (is_dir($dir) === false && @mkdir($dir, $mode, true) === false && is_dir($dir) === false) { // intentionally @; not atomic
				throw new FileException("Unable to create directory [$dir].");
			}
		}

		/**
		 * copies a file or directory
		 *
		 * @param string $source
		 * @param string $dest
		 * @param bool $overwrite
		 *
		 * @throws FileException
		 */
		static public function copy(string $source, string $dest, bool $overwrite = true) {
			if (stream_is_local($source) && file_exists($source) === false) {
				throw new FileException ("File or directory [$source] not found.");
			} else if ($overwrite === false && file_exists($dest)) {
				throw new FileException("File or directory [$dest] already exists.");
			} else if (is_dir($source)) {
				static::createDir($dest);
				foreach (new \FilesystemIterator($dest) as $item) {
					static::delete($item);
				}
				foreach (new \RecursiveIteratorIterator($iterator = new \RecursiveDirectoryIterator($source, \RecursiveDirectoryIterator::SKIP_DOTS), \RecursiveIteratorIterator::SELF_FIRST) as $item) {
					if ($item->isDir()) {
						static::createDir($dest . '/' . $iterator->getSubPathname());
						continue;
					}
					static::copy($item, $dest . '/' . $iterator->getSubPathname());
				}
			}
			static::createDir(dirname($dest));
			/** @noinspection PhpUsageOfSilenceOperatorInspection */
			if (is_dir($source) === false && @stream_copy_to_stream(fopen($source, 'r'), fopen($dest, 'w')) === false) {
				throw new FileException("Unable to copy file [$source] to [$dest].");
			}
		}

		/**
		 * renames a file or directory
		 *
		 * @param string $name
		 * @param string $rename
		 * @param bool $overwrite
		 *
		 * @throws FileException
		 */
		static public function rename(string $name, string $rename, bool $overwrite = true) {
			if ($overwrite === false && file_exists($rename)) {
				throw new FileException("File or directory [$rename] already exists.");
			} else if (file_exists($name) === false) {
				throw new FileException("File or directory [$name] not found.");
			}
			static::createDir(dirname($rename));
			static::delete($rename);
			/** @noinspection PhpUsageOfSilenceOperatorInspection */
			if (@rename($name, $rename) === false) {
				throw new FileException("Unable to rename file or directory [$name] to [$rename].");
			}
		}

		/**
		 * create url from the given file/path
		 *
		 * @param string $file
		 *
		 * @return IUrl
		 * @throws FileException
		 */
		static public function url($file) {
			return Url::create('file:///' . ltrim(self::realpath($file, false), '/'));
		}

		/**
		 * return realpath for the given path
		 *
		 * @param string $path
		 * @param bool $required
		 *
		 * @return string
		 * @throws FileException
		 */
		static public function realpath($path, $required = true) {
			if (($real = realpath($path)) === false) {
				if ($required) {
					throw new RealPathException(sprintf('Cannot get real path from given string [%s].', $path));
				}
				$real = $path;
			}
			return self::normalize($real);
		}

		/**
		 * @param string $path
		 *
		 * @return string
		 */
		static public function normalize($path) {
			return rtrim(str_replace([
				'\\',
				'//',
			], [
				'/',
				'/',
			], $path), '/');
		}
	}

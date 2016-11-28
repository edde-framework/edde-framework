<?php
	declare(strict_types = 1);

	namespace Edde\Common;

	/**
	 * Simple autoloader class.
	 */
	class Autoloader {
		/**
		 * simple autoloader based on namespaces and correct class names
		 *
		 * @param string $namespace
		 * @param string $path
		 * @param bool $root loader is in the root of autoloaded sources
		 */
		static public function register($namespace, $path, $root = true) {
			$namespace .= '\\';
			/** @noinspection CallableParameterUseCaseInTypeContextInspection */
			$root = $root ? null : $namespace;
			spl_autoload_register(function ($class) use ($namespace, $path, $root) {
				if (strpos($class, $namespace) === false) {
					return false;
				}
				$file = str_replace([
					$namespace,
					'\\',
				], [
					$root,
					'/',
				], $path . '/' . $class . '.php');
				/**
				 * it's strange, but this is performance boost
				 */
				if (file_exists($file) === false) {
					return false;
				}
				/** @noinspection PhpIncludeInspection */
				include_once $file;
				return class_exists($class, false);
			}, true);
		}
	}

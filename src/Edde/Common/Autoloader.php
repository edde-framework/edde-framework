<?php
	declare(strict_types = 1);

	namespace Edde\Common;

	/**
	 * Simple autoloader class.
	 */
	class Autoloader {
		/**
		 * @var string
		 */
		protected $namespace;
		/**
		 * @var string
		 */
		protected $path;
		protected $root;

		/**
		 * @param string $namespace
		 * @param string $path
		 * @param bool $root
		 */
		public function __construct($namespace, $path, $root = true) {
			$this->namespace = $namespace . '\\';
			$this->path = $path;
			$this->root = $root;
		}

		/**
		 * simple autoloader based on namespaces and correct class names
		 *
		 * @param string $namespace
		 * @param string $path
		 * @param bool $root loader is in the root of autoloaded sources
		 *
		 * @return Autoloader
		 */
		static public function register($namespace, $path, $root = true) {
			spl_autoload_register($autoloader = new self($namespace, $path, $root), true);
			return $autoloader;
		}

		/**
		 * magic executor of class inclusion
		 *
		 * @param string $class
		 *
		 * @return bool
		 */
		public function __invoke(string $class) {
			if (strpos($class, $this->namespace) === false) {
				return false;
			}
			/** @noinspection PhpUsageOfSilenceOperatorInspection */
			/** @noinspection UsingInclusionOnceReturnValueInspection */
			/** @noinspection UsingInclusionReturnValueInspection */
			@include_once str_replace([
				$this->namespace,
				'\\',
			], [
				$this->root ? null : $this->namespace,
				'/',
			], $this->path . '/' . $class . '.php');
			return class_exists($class, false);
		}
	}

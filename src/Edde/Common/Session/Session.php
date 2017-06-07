<?php
	declare(strict_types=1);

	namespace Edde\Common\Session;

	use ArrayIterator;
	use Edde\Api\Collection\IList;
	use Edde\Api\Session\ISession;
	use Edde\Common\Object;
	use Traversable;

	/**
	 * Session section for simple session data manipulation.
	 */
	class Session extends Object implements ISession {
		/**
		 * @var string
		 */
		protected $namespace;
		protected $name;

		/**
		 * Q: Why did the computer go to the dentist?
		 * A: Because it had Bluetooth.
		 *
		 * @param string $namespace
		 * @param string $name
		 */
		public function __construct(string $namespace, string $name) {
			$this->namespace = $namespace;
			$this->name = $name;
		}

		public function isEmpty(): bool {
			return empty($_SESSION[$this->namespace]);
		}

		public function put(array $array): IList {
			array_merge($_SESSION[$this->namespace], $array);
			return $this;
		}

		public function set(string $name, $value): IList {
			$_SESSION[$this->namespace][$name] = $value;
			return $this;
		}

		public function add(string $name, $value, $key = null): IList {
			return $this;
		}

		public function has(string $name): bool {
			return isset($_SESSION[$this->namespace][$name]);
		}

		public function get(string $name, $default = null) {
			return isset($_SESSION[$this->namespace][$name]) ? $_SESSION[$this->namespace][$name] : $default;
		}

		public function array(): array {
			return $_SESSION[$this->namespace];
		}

		public function remove(string $name): IList {
			unset($_SESSION[$this->namespace][$name]);
			return $this;
		}

		public function getIterator() {
			return new ArrayIterator($_SESSION[$this->namespace]);
		}
	}

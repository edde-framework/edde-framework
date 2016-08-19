<?php
	declare(strict_types = 1);

	namespace Edde\Common\Session;

	use Edde\Api\Collection\IList;
	use Edde\Api\Session\ISession;
	use Edde\Api\Session\ISessionManager;
	use Edde\Common\Collection\AbstractList;
	use Edde\Common\Usable\UsableTrait;

	class Session extends AbstractList implements ISession {
		use UsableTrait;
		/**
		 * @var ISessionManager
		 */
		protected $sessionManager;
		/**
		 * @var string
		 */
		protected $name;

		/**
		 * @param ISessionManager $sessionManager
		 * @param string $name
		 */
		public function __construct(ISessionManager $sessionManager, string $name) {
			$this->sessionManager = $sessionManager;
			$this->name = $name;
		}

		public function isEmpty(): bool {
			$this->use();
			return parent::isEmpty();
		}

		public function set(string $name, $value): IList {
			$this->use();
			return parent::set($name, $value);
		}

		public function get(string $name, $default = null) {
			$this->use();
			return parent::get($name, $default);
		}

		public function has(string $name): bool {
			$this->use();
			return parent::has($name);
		}

		public function array(): array {
			$this->use();
			return $this->list;
		}

		public function remove(string $name): IList {
			$this->use();
			return parent::remove($name);
		}

		public function getIterator() {
			$this->use();
			return parent::getIterator();
		}

		protected function prepare() {
			$this->list = &$this->sessionManager->session($this->name);
		}
	}

<?php
	declare(strict_types = 1);

	namespace Edde\Common\Session;

	use Edde\Api\Session\ISession;
	use Edde\Api\Session\ISessionManager;
	use Edde\Common\Usable\AbstractUsable;

	class Session extends AbstractUsable implements ISession {
		/**
		 * @var ISessionManager
		 */
		protected $sessionManager;
		/**
		 * @var string
		 */
		protected $name;
		/**
		 * @var array
		 */
		protected $session;

		/**
		 * @param ISessionManager $sessionManager
		 * @param string $name
		 */
		public function __construct(ISessionManager $sessionManager, string $name) {
			$this->sessionManager = $sessionManager;
			$this->name = $name;
		}

		public function isset(string $name): bool {
			$this->use();
			return isset($this->session[$name]);
		}

		public function set(string $name, $value): ISession {
			$this->use();
			$this->session[$name] = $value;
			return $this;
		}

		public function get(string $name, $default = null): ISession {
			$this->use();
			return $this->session[$name] ?? $default;
		}

		public function array(): array {
			return $this->session;
		}

		protected function prepare() {
			$this->session = &$this->sessionManager->session($this->name);
		}
	}

<?php
	declare(strict_types = 1);

	namespace Edde\Common\Session;

	use Edde\Api\Session\ISession;
	use Edde\Api\Session\ISessionManager;
	use Edde\Api\Session\SessionException;
	use Edde\Common\Usable\AbstractUsable;

	class SessionManager extends AbstractUsable implements ISessionManager {
		/**
		 * @var string
		 */
		protected $namespace;

		protected $sessionId;
		/**
		 * @var ISession[]
		 */
		protected $sessionList = [];

		/**
		 * @param string $namespace
		 */
		public function __construct($namespace = null) {
			$this->namespace = $namespace ?: 'edde';
		}

		public function getSession(string $name): ISession {
			return $this->sessionList[$name] ?? $this->sessionList[$name] = new Session($this, $name);
		}

		public function &session(string $name): array {
			$this->use();
			$this->start();
			$_SESSION[$this->namespace] = $_SESSION[$this->namespace] ?? [];
			$_SESSION[$this->namespace][$name] = $_SESSION[$this->namespace][$name] ?? [];
			return $_SESSION[$this->namespace][$name];
		}

		public function start(string $sessionId = null): ISessionManager {
			if ($this->isSession()) {
				return $this;
			}
			if ($sessionId !== null) {
				$this->setSessionId($sessionId);
			}
			if ($this->sessionId !== null) {
				session_id($this->sessionId);
			}
			session_start();
			return $this;
		}

		public function isSession(): bool {
			return session_status() === PHP_SESSION_ACTIVE;
		}

		public function setSessionId(string $sessionId = null): ISessionManager {
			$this->sessionId = $sessionId;
			return $this;
		}

		public function close(): ISessionManager {
			if ($this->isSession() === false) {
				throw new SessionException('Session is not running; there is nothing to close.');
			}
			session_write_close();
			return $this;
		}

		protected function prepare() {
		}
	}

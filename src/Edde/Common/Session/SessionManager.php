<?php
	declare(strict_types = 1);

	namespace Edde\Common\Session;

	use Edde\Api\Session\IFingerprint;
	use Edde\Api\Session\ISession;
	use Edde\Api\Session\ISessionManager;
	use Edde\Api\Session\SessionException;
	use Edde\Common\Usable\AbstractUsable;

	class SessionManager extends AbstractUsable implements ISessionManager {
		/**
		 * @var IFingerprint
		 */
		protected $fingerprint;
		/**
		 * @var string
		 */
		protected $namespace;
		/**
		 * @var ISession[]
		 */
		protected $sessionList = [];

		public function __construct(IFingerprint $fingerprint) {
			$this->fingerprint = $fingerprint;
			$this->namespace = 'edde';
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

		public function start(): ISessionManager {
			if ($this->isSession()) {
				return $this;
			}
			if (($fingerprint = $this->fingerprint->generate()) !== null) {
				session_id($fingerprint);
			}
			session_start();
			return $this;
		}

		public function isSession(): bool {
			return session_status() === PHP_SESSION_ACTIVE;
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

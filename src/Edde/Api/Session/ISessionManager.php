<?php
	declare(strict_types = 1);

	namespace Edde\Api\Session;

	use Edde\Api\Usable\IUsable;

	/**
	 * Session manager is responsible for updating session state (starting, modifying, closing, ...).
	 */
	interface ISessionManager extends IUsable {
		/**
		 * optionaly sets session id without starting
		 *
		 * @param string $sessionId
		 *
		 * @return ISessionManager
		 */
		public function setSessionId(string $sessionId = null): ISessionManager;

		/**
		 * excplicitly open a session
		 *
		 * @param string $sessionId an optional id
		 *
		 * @return ISessionManager
		 */
		public function start(string $sessionId = null): ISessionManager;

		/**
		 * tells if session is opened
		 *
		 * @return bool
		 */
		public function isSession(): bool;

		/**
		 * return a new session with the given name; this may start a session
		 *
		 * @param string $name
		 *
		 * @return ISession
		 */
		public function getSession(string $name): ISession;

		/**
		 * return reference to the current session root ($_SESSION superglobal)
		 *
		 * @param string $name
		 *
		 * @return array
		 */
		public function &session(string $name): array;

		/**
		 * excplicitly close a session (to release session locks)
		 *
		 * @return ISessionManager
		 */
		public function close(): ISessionManager;
	}

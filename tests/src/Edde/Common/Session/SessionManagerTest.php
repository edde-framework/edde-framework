<?php
	declare(strict_types = 1);

	namespace Edde\Common\Session;

	use Edde\Api\Session\ISessionManager;
	use phpunit\framework\TestCase;

	class SessionManagerTest extends TestCase {
		/**
		 * @var ISessionManager
		 */
		protected $sessionManager;

		public function testBasicWorkflow() {
			self::assertFalse($this->sessionManager->isSession());
			$this->sessionManager->start('foo');
			self::assertTrue($this->sessionManager->isSession());
			$session = $this->sessionManager->getSession('section');
			$session->set('poo', 'blabla');
			self::assertEquals($_SESSION['edde']['section']['poo'], 'blabla');
		}

		protected function setUp() {
			$this->sessionManager = new SessionManager();
			ini_set('session.use_cookies', 'off');
			ini_set('session.use_only_cookies', 'off');
			ini_set('session.use_trans_sid', 'on');
			ini_set('session.cache_limiter', '');
		}

		protected function tearDown() {
			ob_end_flush();
		}
	}

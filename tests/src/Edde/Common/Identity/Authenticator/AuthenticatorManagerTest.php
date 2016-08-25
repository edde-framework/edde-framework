<?php
	declare(strict_types = 1);

	namespace Edde\Common\Identity\Authenticator;

	use Edde\Api\Identity\Authenticator\IAuthenticatorManager;
	use Edde\Api\Identity\IIdentity;
	use Edde\Common\Identity\Identity;
	use Edde\Common\Session\DummyFingerprint;
	use Edde\Common\Session\SessionManager;
	use phpunit\framework\TestCase;

	require_once(__DIR__ . '/assets/assets.php');

	class AuthenticatorManagerTest extends TestCase {
		/**
		 * @var IAuthenticatorManager
		 */
		protected $authenticatorManager;
		/**
		 * @var IIdentity
		 */
		protected $identity;

		public function testFlow() {
			self::assertEquals('unknown', $this->identity->getName());
			self::assertFalse($this->identity->isAuthenticated());
			$this->authenticatorManager->flow('flow', $this->identity, 'foo', 'bar');
			self::assertEquals('whepee', $this->identity->getName());
			self::assertFalse($this->identity->isAuthenticated());
			self::assertEquals(\SecondaryAuthenticator::class, $this->authenticatorManager->getCurrentFlow());
			$this->authenticatorManager->flow('flow', $this->identity, 'boo', 'poo');
			self::assertEquals('whepee', $this->identity->getName());
			self::assertTrue($this->identity->isAuthenticated());
			self::assertNull($this->authenticatorManager->getCurrentFlow());
		}

		protected function setUp() {
			$this->authenticatorManager = new AuthenticatorManager();
			$this->authenticatorManager->injectSessionManager(new SessionManager(new DummyFingerprint()));
			$this->authenticatorManager->registerAuthenticator(new \TrustedAuthenticator());
			$this->authenticatorManager->registerAuthenticator(new \InitialAuthenticator());
			$this->authenticatorManager->registerAuthenticator(new \SecondaryAuthenticator());
			$this->authenticatorManager->registerFlow('flow', \InitialAuthenticator::class, \SecondaryAuthenticator::class);
			$this->identity = new Identity();

			/**
			 * because of session usage in tests
			 */
			ini_set('session.use_cookies', 'off');
			ini_set('session.use_only_cookies', 'off');
			ini_set('session.use_trans_sid', 'on');
			ini_set('session.cache_limiter', '');
		}

		protected function tearDown() {
			ob_end_flush();
		}
	}

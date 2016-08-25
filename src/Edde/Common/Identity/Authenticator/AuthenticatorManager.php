<?php
	declare(strict_types = 1);

	namespace Edde\Common\Identity\Authenticator;

	use Edde\Api\Identity\Authenticator\AuthenticatorException;
	use Edde\Api\Identity\Authenticator\IAuthenticator;
	use Edde\Api\Identity\Authenticator\IAuthenticatorManager;
	use Edde\Api\Identity\IIdentity;
	use Edde\Common\Container\LazyInjectTrait;
	use Edde\Common\Identity\AbstractAuthManager;
	use Edde\Common\Session\SessionTrait;

	class AuthenticatorManager extends AbstractAuthManager implements IAuthenticatorManager {
		use LazyInjectTrait;
		use SessionTrait;

		/**
		 * @var IAuthenticator[]
		 */
		protected $authenticatorList = [];
		/**
		 * @var string[][]
		 */
		protected $flowList = [];

		public function registerAuthenticator(IAuthenticator $authenticator): IAuthenticatorManager {
			$this->authenticatorList[$authenticator->getName()] = $authenticator;
			return $this;
		}

		public function registerFlow(string $initial, string ...$authenticatorList): IAuthenticatorManager {
			$this->flowList[$initial] = array_merge([$initial], $authenticatorList);
			return $this;
		}

		public function flow(string $flow, IIdentity $identity = null, ...$credentials): IAuthenticatorManager {
			$this->use();
			if (isset($this->flowList[$flow]) === false) {
				throw new AuthenticatorException(sprintf('Cannot run authentification flow - unknown flow [%s],', $flow));
			}
			$current = $this->session->get('flow', $this->flowList[$flow]);
			$this->authenticate(array_shift($current), $identity, ...$credentials);
			$this->session->set('flow', $current);
			if (empty($current)) {
				$this->reset($flow);
			}
			return $this;
		}

		public function authenticate(string $name, IIdentity $identity = null, ...$credentials): IAuthenticatorManager {
			$this->use();
			if (isset($this->authenticatorList[$name]) === false) {
				throw new AuthenticatorException(sprintf('Cannot authenticate identity by unknown authenticator [%s]; did you registered it before?', $name));
			}
			$this->authenticatorList[$name]->authenticate($identity ?: $this->identity, ...$credentials);
			return $this;
		}

		public function reset(string $flow): IAuthenticatorManager {
			if (isset($this->flowList[$flow]) === false) {
				throw new AuthenticatorException(sprintf('Cannot reset authentification flow - unknown flow [%s],', $flow));
			}
			$this->session->set('flow', null);
			return $this;
		}

		public function getCurrentFlow() {
			if ($this->hasFlow() === false) {
				return null;
			}
			$flow = $this->getFlow();
			return reset($flow);
		}

		public function hasFlow(): bool {
			return $this->session->get('flow', false) !== false;
		}

		public function getFlow(): array {
			return $this->session->get('flow', []);
		}

		protected function prepare() {
			$this->session();
			foreach ($this->flowList as $name => $authList) {
				if (isset($this->authenticatorList[$name]) === false) {
					throw new AuthenticatorException(sprintf('Unknown authenticator [%s] in flow.', $name));
				}
				foreach ($authList as $authenticator) {
					if (isset($this->authenticatorList[$authenticator]) === false) {
						throw new AuthenticatorException(sprintf('Unknown authenticator [%s] in flow [%s].', $authenticator, $name));
					}
				}
			}
		}
	}

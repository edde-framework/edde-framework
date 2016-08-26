<?php
	declare(strict_types = 1);

	namespace Edde\Common\Identity;

	use Edde\Api\Identity\AuthenticatorException;
	use Edde\Api\Identity\IAuthenticator;
	use Edde\Api\Identity\IAuthenticatorManager;
	use Edde\Api\Identity\IAuthorizator;
	use Edde\Api\Identity\IIdentity;
	use Edde\Common\Session\SessionTrait;
	use Edde\Common\Usable\AbstractUsable;

	class AuthenticatorManager extends AbstractUsable implements IAuthenticatorManager {
		use SessionTrait;
		/**
		 * @var IAuthenticator[]
		 */
		protected $authenticatorList = [];
		/**
		 * @var string[][]
		 */
		protected $flowList = [];
		/**
		 * @var IAuthorizator
		 */
		protected $authorizator;
		/**
		 * @var IIdentity
		 */
		protected $identity;

		public function lazyAutorizator(IAuthorizator $authorizator) {
			$this->authorizator = $authorizator;
		}

		public function registerAuthenticator(IAuthenticator $authenticator): IAuthenticatorManager {
			$this->authenticatorList[$authenticator->getName()] = $authenticator;
			return $this;
		}

		public function registerFlow(string $initial, string ...$authenticatorList): IAuthenticatorManager {
			$this->flowList[$initial] = $authenticatorList;
			return $this;
		}

		public function flow(string $flow, ...$credentials): IAuthenticatorManager {
			$this->use();
			if (($currentList = $this->session->get('flow', false)) === false) {
				throw new AuthenticatorException(sprintf('Flow was not started; please use [%s::select()] method before.', static::class));
			}
			if (($current = array_shift($currentList)) !== $flow) {
				throw new AuthenticatorException(sprintf('Unexpected authentification method [%s]; current method [%s].', $flow, $current));
			}
			$this->authenticate($current, ...$credentials);
			$this->session->set('flow', $currentList);
			if (empty($currentList)) {
				$this->identity->setAuthenticated(true);
				$this->authorizator->authorize($this->identity);
				$this->reset();
			}
			$this->session->set('identity', $this->identity);
			return $this;
		}

		public function authenticate(string $name, ...$credentials): IAuthenticatorManager {
			$this->use();
			if (isset($this->authenticatorList[$name]) === false) {
				throw new AuthenticatorException(sprintf('Cannot authenticate identity by unknown authenticator [%s]; did you registered it before?', $name));
			}
			$this->authenticatorList[$name]->authenticate($this->identity, ...$credentials);
			return $this;
		}

		public function reset(): IAuthenticatorManager {
			$this->use();
			$this->session->set('flow', null);
			$this->session->set('identity', null);
			return $this;
		}

		public function select(string $flow): IAuthenticatorManager {
			$this->reset();
			$this->session->set('flow', $this->flowList[$flow]);
			return $this;
		}

		public function getCurrentFlow() {
			$this->use();
			if ($this->hasFlow() === false) {
				return null;
			}
			$flow = $this->getFlow();
			return reset($flow);
		}

		public function hasFlow(): bool {
			$this->use();
			return $this->session->get('flow', false) !== false;
		}

		public function getFlow(): array {
			$this->use();
			return $this->session->get('flow', []);
		}

		protected function prepare() {
			$this->identity = $this->identity();
			foreach ($this->flowList as $name => $authList) {
				foreach ($authList as $authenticator) {
					if (isset($this->authenticatorList[$authenticator]) === false) {
						throw new AuthenticatorException(sprintf('Unknown authenticator [%s] in flow [%s].', $authenticator, $name));
					}
				}
			}
		}

		public function identity(): IIdentity {
			if ($this->identity === null) {
				$this->identity = $this->session->get('identity', new Identity());
			}
			return $this->identity;
		}
	}

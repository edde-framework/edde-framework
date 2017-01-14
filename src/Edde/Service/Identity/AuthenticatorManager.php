<?php
	declare(strict_types = 1);

	namespace Edde\Service\Identity;

	use Edde\Api\Identity\AuthenticatorException;
	use Edde\Api\Identity\IAuthenticator;
	use Edde\Api\Identity\IAuthenticatorManager;
	use Edde\Api\Identity\LazyAuthorizatorTrait;
	use Edde\Api\Identity\LazyIdentityManagerTrait;
	use Edde\Api\Identity\LazyIdentityTrait;
	use Edde\Common\Object;
	use Edde\Common\Session\SessionTrait;

	class AuthenticatorManager extends Object implements IAuthenticatorManager {
		use LazyIdentityTrait;
		use LazyIdentityManagerTrait;
		use LazyAuthorizatorTrait;
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

		public function registerStepList(array $stepList): IAuthenticatorManager {
			foreach ($stepList as $name => $flow) {
				if (is_string($flow)) {
					$name = $flow;
					$flow = [$flow];
				}
				$this->registerStep($name, ...$flow);
			}
			return $this;
		}

		public function registerStep(string $initial, string ...$authenticatorList): IAuthenticatorManager {
			$this->flowList[$initial] = empty($authenticatorList) ? [$initial] : $authenticatorList;
			return $this;
		}

		public function step(string $step, ...$credentials): IAuthenticatorManager {
			if (($currentList = $this->session->get('flow', false)) === false) {
				throw new AuthenticatorException(sprintf('Flow was not started; please use [%s::select()] method before.', static::class));
			}
			if (($current = array_shift($currentList)) !== $step) {
				throw new AuthenticatorException(sprintf('Unexpected authentification method [%s]; current method [%s].', $step, $current));
			}
			$this->authenticate($current, ...$credentials);
			$this->session->set('flow', $currentList);
			if (empty($currentList)) {
				$this->identity->setAuthenticated(true);
				$this->authorizator->authorize($this->identity);
				$this->session->set('flow', null);
			}
			$this->identityManager->update();
			return $this;
		}

		public function authenticate(string $name, ...$credentials): IAuthenticatorManager {
			if (isset($this->authenticatorList[$name]) === false) {
				throw new AuthenticatorException(sprintf('Cannot authenticate identity by unknown authenticator [%s]; did you registered it before?', $name));
			}
			$this->authenticatorList[$name]->authenticate($this->identity, ...$credentials);
			return $this;
		}

		public function select(string $step): IAuthenticatorManager {
			$this->reset();
			if (isset($this->flowList[$step]) === false) {
				throw new AuthenticatorException(sprintf('Requested unknown flow [%s]; did you registered it?', $step));
			}
			$this->session->set('flow', $this->flowList[$step]);
			return $this;
		}

		public function reset(): IAuthenticatorManager {
			$this->session->set('flow', null);
			$this->identityManager->reset(true);
			return $this;
		}

		public function getCurrentStep(): string {
			if ($this->isDone()) {
				throw new AuthenticatorException('There are no more steps!');
			}
			$flow = $this->getStepList();
			return reset($flow);
		}

		public function isDone(): bool {
			return $this->session->get('flow', false) === false;
		}

		public function getStepList(): array {
			return $this->session->get('flow', []);
		}

		protected function prepare() {
			parent::prepare();
			foreach ($this->flowList as $name => $authList) {
				foreach ($authList as $authenticator) {
					if (isset($this->authenticatorList[$authenticator]) === false) {
						throw new AuthenticatorException(sprintf('Unknown authenticator [%s] in flow [%s].', $authenticator, $name));
					}
				}
			}
			$this->session();
		}
	}

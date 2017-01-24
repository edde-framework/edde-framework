<?php
	declare(strict_types=1);

	namespace Edde\Common\Session;

	use Edde\Api\Collection\IList;
	use Edde\Api\Session\ISession;
	use Edde\Api\Session\ISessionManager;
	use Edde\Common\Collection\AbstractList;

	/**
	 * Session section for simple session data manipulation.
	 */
	class Session extends AbstractList implements ISession {
		/**
		 * @var ISessionManager
		 */
		protected $sessionManager;
		/**
		 * @var string
		 */
		protected $name;

		/**
		 * Q: Why did the computer go to the dentist?
		 * A: Because it had Bluetooth.
		 *
		 * @param ISessionManager $sessionManager
		 * @param string          $name
		 */
		public function __construct(ISessionManager $sessionManager, string $name) {
			parent::__construct();
			$this->sessionManager = $sessionManager;
			$this->name = $name;
		}

		/**
		 * @inheritdoc
		 */
		public function set(string $name, $value): IList {
			if ($value === null) {
				return parent::remove($name);
			}
			return parent::set($name, $value);
		}

		/**
		 * @inheritdoc
		 */
		protected function prepare() {
			parent::prepare();
			$this->list = &$this->sessionManager->session($this->name);
		}
	}

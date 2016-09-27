<?php
	declare(strict_types = 1);

	namespace Edde\Common\Session;

	use Edde\Api\Collection\IList;
	use Edde\Api\Session\ISession;
	use Edde\Api\Session\ISessionManager;
	use Edde\Common\Collection\AbstractList;
	use Edde\Common\Deffered\DefferedTrait;

	/**
	 * Session section for simple session data manipulation.
	 */
	class Session extends AbstractList implements ISession {
		use DefferedTrait;
		/**
		 * @var ISessionManager
		 */
		protected $sessionManager;
		/**
		 * @var string
		 */
		protected $name;

		/**
		 * @param ISessionManager $sessionManager
		 * @param string $name
		 */
		public function __construct(ISessionManager $sessionManager, string $name) {
			$this->sessionManager = $sessionManager;
			$this->name = $name;
		}

		/**
		 * @inheritdoc
		 */
		public function isEmpty(): bool {
			$this->use();
			return parent::isEmpty();
		}

		/**
		 * @inheritdoc
		 */
		public function set(string $name, $value): IList {
			$this->use();
			if ($value === null) {
				return parent::remove($name);
			}
			return parent::set($name, $value);
		}

		/**
		 * @inheritdoc
		 */
		public function get(string $name, $default = null) {
			$this->use();
			return parent::get($name, $default);
		}

		/**
		 * @inheritdoc
		 */
		public function has(string $name): bool {
			$this->use();
			return parent::has($name);
		}

		/** @noinspection PhpMissingParentCallCommonInspection */
		/**
		 * @inheritdoc
		 */
		public function array(): array {
			$this->use();
			return $this->list;
		}

		/**
		 * @inheritdoc
		 */
		public function remove(string $name): IList {
			$this->use();
			return parent::remove($name);
		}

		/**
		 * @inheritdoc
		 */
		public function getIterator() {
			$this->use();
			return parent::getIterator();
		}

		/**
		 * @inheritdoc
		 */
		protected function prepare() {
			$this->list = &$this->sessionManager->session($this->name);
		}
	}

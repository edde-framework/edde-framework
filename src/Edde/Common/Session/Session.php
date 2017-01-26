<?php
	declare(strict_types=1);

	namespace Edde\Common\Session;

	use Edde\Api\Session\ISession;
	use Edde\Common\Collection\AbstractList;

	/**
	 * Session section for simple session data manipulation.
	 */
	class Session extends AbstractList implements ISession {
		/**
		 * @var string
		 */
		protected $name;

		/**
		 * Q: Why did the computer go to the dentist?
		 * A: Because it had Bluetooth.
		 *
		 * @param array  $session
		 * @param string $name
		 */
		public function __construct(array &$session, string $name) {
			parent::__construct();
			$this->list = $session;
			$this->name = $name;
		}
	}

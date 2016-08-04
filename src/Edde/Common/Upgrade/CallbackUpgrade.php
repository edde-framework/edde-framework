<?php
	declare(strict_types = 1);

	namespace Edde\Common\Upgrade;

	/**
	 * This can be used as an adhoc upgrade.
	 */
	class CallbackUpgrade extends AbstractUpgrade {
		/**
		 * @var callable
		 */
		protected $callback;

		/**
		 * @param callable $callback
		 * @param string $version
		 */
		public function __construct(callable $callback, $version) {
			parent::__construct($version);
			$this->callback = $callback;
		}

		protected function onUpgrade() {
			call_user_func($this->callback);
		}

		protected function prepare() {
		}
	}

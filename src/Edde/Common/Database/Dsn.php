<?php
	declare(strict_types=1);

	namespace Edde\Common\Database;

	class Dsn extends AbstractDsn {
		/**
		 * @var string
		 */
		protected $dsn;

		/**
		 * Two students talk:
		 * "What are you reading?"
		 * "Quantum physics theory book."
		 * "But why are you reading it upside-down?"
		 * "It makes no difference anyway."
		 *
		 * @param string $dsn
		 */
		public function __construct(string $dsn) {
			$this->dsn = $dsn;
		}

		public function getDsn(): string {
			return $this->dsn;
		}
	}

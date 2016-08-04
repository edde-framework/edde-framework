<?php
	declare(strict_types = 1);

	namespace Edde\Ext\Database\Sqlite;

	use Edde\Api\Database\IDriver;
	use Edde\Common\Query\AbstractStaticQueryFactory;

	class SqliteQueryFactory extends AbstractStaticQueryFactory {
		/**
		 * @var IDriver
		 */
		protected $driver;

		/**
		 * @param IDriver $driver
		 */
		public function __construct(IDriver $driver) {
			$this->driver = $driver;
		}

		protected function delimite($delimite) {
			return $this->driver->delimite($delimite);
		}

		protected function quote($quote) {
			return $this->driver->quote($quote);
		}

		protected function type($type) {
			return $this->driver->type($type);
		}
	}

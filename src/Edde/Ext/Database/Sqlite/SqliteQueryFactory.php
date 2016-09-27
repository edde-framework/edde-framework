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

		/**
		 * @inheritdoc
		 */
		protected function delimite(string $delimite): string {
			return $this->driver->delimite($delimite);
		}

		/**
		 * @inheritdoc
		 */
		protected function quote(string $quote): string {
			return $this->driver->quote($quote);
		}

		/**
		 * @inheritdoc
		 */
		protected function type(string $type): string {
			return $this->driver->type($type);
		}
	}

<?php
	declare(strict_types = 1);

	use Edde\Api\Crate\ICrate;
	use Edde\Api\Query\IQuery;
	use Edde\Api\Storage\IStorage;
	use Edde\Common\Storage\AbstractStorage;

	class DummyStorage extends AbstractStorage {
		public function start($exclusive = false): IStorage {
			return $this;
		}

		public function commit(): IStorage {
			return $this;
		}

		public function rollback(): IStorage {
			return $this;
		}

		public function execute(IQuery $query) {
		}

		public function store(ICrate $crate): IStorage {
			return $this;
		}

		protected function prepare() {
		}
	}
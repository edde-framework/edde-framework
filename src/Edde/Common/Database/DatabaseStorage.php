<?php
	namespace Edde\Common\Database;

	use Edde\Api\Query\IQuery;
	use Edde\Api\Storage\IStorable;
	use Edde\Common\Storage\AbstractStorage;

	class DatabaseStorage extends AbstractStorage {
		public function collection(IQuery $query) {
		}

		public function store(IStorable $storable) {
		}

		protected function prepare() {
		}
	}

<?php
	declare(strict_types = 1);

	namespace Edde\Api\Storage;

	use Edde\Api\Crate\ICrate;
	use Edde\Common\Query\Select\SelectQuery;
	use Edde\Common\Storage\BoundQuery;

	trait RepositoryTrait {
		public function store(ICrate $crate): IRepository {
			$this->storage->store($crate);
			return $this;
		}

		public function bound(string $query, ...$parameterList): IBoundQuery {
			return (new BoundQuery())->bind($this->container->create($query, ...$parameterList), $this->storage);
		}

		public function query(): IBoundQuery {
			return $this->bound(SelectQuery::class);
		}
	}

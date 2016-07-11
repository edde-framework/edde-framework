<?php
	namespace Edde\Common\Storage;

	use Edde\Api\Query\IQuery;
	use Edde\Api\Storage\IStorage;
	use Edde\Api\Storage\StorageException;
	use Edde\Common\Usable\AbstractUsable;

	abstract class AbstractStorage extends AbstractUsable implements IStorage {
		public function storable(IQuery $query) {
			foreach ($this->collection($query) as $storable) {
				return $storable;
			}
			throw new StorageException('Cannot retrieve any storable by the given query.');
		}
	}

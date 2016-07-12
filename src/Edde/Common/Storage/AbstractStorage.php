<?php
	namespace Edde\Common\Storage;

	use Edde\Api\Container\IContainer;
	use Edde\Api\Query\IQuery;
	use Edde\Api\Schema\ISchema;
	use Edde\Api\Storage\IStorage;
	use Edde\Api\Storage\StorageException;
	use Edde\Common\Usable\AbstractUsable;

	abstract class AbstractStorage extends AbstractUsable implements IStorage {
		/**
		 * @var IContainer
		 */
		protected $container;

		/**
		 * @param IContainer $container
		 */
		public function __construct(IContainer $container) {
			$this->container = $container;
		}

		public function storable(ISchema $schema, IQuery $query) {
			foreach ($this->collection($schema, $query) as $storable) {
				return $storable;
			}
			throw new StorageException(sprintf('Cannot retrieve any storable [%s] by the given query.', $schema->getSchemaName()));
		}

		public function collection(ISchema $schema, IQuery $query) {
			return new Collection($schema, $this, $this->container, $query);
		}

	}

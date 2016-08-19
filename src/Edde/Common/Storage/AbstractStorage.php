<?php
	declare(strict_types = 1);

	namespace Edde\Common\Storage;

	use Edde\Api\Container\IContainer;
	use Edde\Api\Crate\ICrate;
	use Edde\Api\Query\IQuery;
	use Edde\Api\Schema\ISchema;
	use Edde\Api\Schema\ISchemaManager;
	use Edde\Api\Storage\ICollection;
	use Edde\Api\Storage\IStorage;
	use Edde\Api\Storage\StorageException;
	use Edde\Common\Container\LazyInjectTrait;
	use Edde\Common\Query\Select\SelectQuery;
	use Edde\Common\Usable\AbstractUsable;

	abstract class AbstractStorage extends AbstractUsable implements IStorage {
		use LazyInjectTrait;
		/**
		 * @var IContainer
		 */
		protected $container;
		/**
		 * @var ISchemaManager
		 */
		protected $schemaManager;

		/**
		 * @param IContainer $container
		 */
		public function __construct(IContainer $container) {
			$this->container = $container;
		}

		public function lazySchemaManager(ISchemaManager $schemaManager) {
			$this->schemaManager = $schemaManager;
		}

		public function load(string $schema, IQuery $query) {
			foreach ($this->collection($schema, $query) as $crate) {
				return $crate;
			}
			throw new StorageException(sprintf('Cannot retrieve any crate [%s] by the given query.', $schema->getSchemaName()));
		}

		public function collection(string $schema, IQuery $query = null): ICollection {
			if ($query === null) {
				$query = new SelectQuery();
				$query->select()
					->all()
					->from()
					->source($schema);
			}
			return new Collection($this->schemaManager->getSchema($schema), $this, $this->container, $query);
		}

		public function collectionTo(ICrate $crate, ISchema $relation, string $source, string $target): ICollection {
			$sourceLink = $relation->getLink($source);
			$targetLink = $relation->getLink($target);
			$targetSchema = $targetLink->getTarget()
				->getSchema();
			$targetSchemaName = $targetSchema->getSchemaName();
			$selectQuery = new SelectQuery();
			$relationAlias = sha1(random_bytes(64));
			$targetAlias = sha1(random_bytes(64));
			foreach ($targetSchema->getPropertyList() as $schemaProperty) {
				$selectQuery->select()
					->property($schemaProperty->getName(), $targetAlias);
			}
			$selectQuery->from()
				->source($relation->getSchemaName(), $relationAlias)
				->source($targetSchemaName, $targetAlias)
				->where()
				->eq()
				->property($sourceLink->getSource()
					->getName(), $relationAlias)
				->parameter($crate->get($sourceLink->getTarget()
					->getName()))
				->and()
				->eq()
				->property($targetLink->getSource()
					->getName(), $relationAlias)
				->property($targetLink->getTarget()
					->getName(), $targetAlias);
			return $this->collection($targetSchemaName, $selectQuery);
		}
	}

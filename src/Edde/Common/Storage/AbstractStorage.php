<?php
	declare(strict_types = 1);

	namespace Edde\Common\Storage;

	use Edde\Api\Container\IContainer;
	use Edde\Api\Crate\ICrate;
	use Edde\Api\Query\IQuery;
	use Edde\Api\Schema\ISchema;
	use Edde\Api\Storage\ICollection;
	use Edde\Api\Storage\IStorage;
	use Edde\Api\Storage\StorageException;
	use Edde\Common\Query\Select\SelectQuery;
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

		public function load(ISchema $schema, IQuery $query) {
			foreach ($this->collection($schema, $query) as $crate) {
				return $crate;
			}
			throw new StorageException(sprintf('Cannot retrieve any crate [%s] by the given query.', $schema->getSchemaName()));
		}

		public function collection(ISchema $schema, IQuery $query = null): ICollection {
			if ($query === null) {
				$query = new SelectQuery();
				$query->select()
					->all()
					->from()
					->source($schema->getSchemaName());
			}
			return new Collection($schema, $this, $this->container, $query);
		}

		public function collectionTo(ICrate $crate, ISchema $relation, string $source, string $target): ICollection {
			$sourceLink = $relation->getLink($source);
			$targetLink = $relation->getLink($target);
			$targetSchema = $targetLink->getTarget()
				->getSchema();
			$selectQuery = new SelectQuery();
			$relationAlias = sha1(random_bytes(64));
			$targetAlias = sha1(random_bytes(64));
			foreach ($targetSchema->getPropertyList() as $schemaProperty) {
				$selectQuery->select()
					->property($schemaProperty->getName(), $targetAlias);
			}
			$selectQuery->from()
				->source($relation->getSchemaName(), $relationAlias)
				->source($targetSchema->getSchemaName(), $targetAlias)
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
			return $this->collection($targetSchema, $selectQuery);
		}
	}

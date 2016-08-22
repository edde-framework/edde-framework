<?php
	declare(strict_types = 1);

	namespace Edde\Common\Storage;

	use Edde\Api\Crate\ICrate;
	use Edde\Api\Crate\ICrateFactory;
	use Edde\Api\Query\IQuery;
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
		 * @var ISchemaManager
		 */
		protected $schemaManager;
		/**
		 * @var ICrateFactory
		 */
		protected $crateFactory;

		public function lazySchemaManager(ISchemaManager $schemaManager) {
			$this->schemaManager = $schemaManager;
		}

		public function lazyCrateFactory(ICrateFactory $crateFactory) {
			$this->crateFactory = $crateFactory;
		}

		public function collectionTo(ICrate $crate, string $relation, string $source, string $target, string $crateTo = null): ICollection {
			$relationSchema = $this->schemaManager->getSchema($relation);
			$sourceLink = $relationSchema->getLink($source);
			$targetLink = $relationSchema->getLink($target);
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
				->source($relationSchema->getSchemaName(), $relationAlias)
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
			return $this->collection($crateTo ?: $targetSchemaName, $selectQuery, $targetSchemaName);
		}

		public function collection(string $crate, IQuery $query = null, string $schema = null): ICollection {
			$schema = $schema ?: $crate;
			if ($query === null) {
				$query = new SelectQuery();
				$query->select()
					->all()
					->from()
					->source($schema);
			}
			return new Collection($crate, $this, $this->crateFactory, $query, $schema);
		}

		public function getLink(ICrate $crate, string $name): ICrate {
			$link = $crate->getSchema()
				->getLink($name);
			$selectQuery = new SelectQuery();
			$targetSchemaName = $link->getTarget()
				->getSchema()
				->getSchemaName();
			$selectQuery->select()
				->all()
				->from()
				->source($targetSchemaName)
				->where()
				->eq()
				->property($link->getTarget()
					->getName())
				->parameter($crate->get($link->getSource()
					->getName()));
			return $this->load($targetSchemaName, $selectQuery);
		}

		public function load(string $crate, IQuery $query, string $schema = null) {
			foreach ($this->collection($crate, $query, $schema) as $item) {
				return $item;
			}
			throw new StorageException(sprintf('Cannot retrieve any crate [%s] by the given query.', $crate));
		}
	}

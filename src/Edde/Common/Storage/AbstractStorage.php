<?php
	declare(strict_types = 1);

	namespace Edde\Common\Storage;

	use Edde\Api\Crate\ICrate;
	use Edde\Api\Crate\ICrateFactory;
	use Edde\Api\Query\IQuery;
	use Edde\Api\Schema\LazySchemaManagerTrait;
	use Edde\Api\Schema\SchemaException;
	use Edde\Api\Storage\EmptyResultException;
	use Edde\Api\Storage\ICollection;
	use Edde\Api\Storage\IStorage;
	use Edde\Common\Deffered\AbstractDeffered;
	use Edde\Common\Query\Select\SelectQuery;

	/**
	 * Base for all storage implementations.
	 */
	abstract class AbstractStorage extends AbstractDeffered implements IStorage {
		use LazySchemaManagerTrait;
		/**
		 * @var ICrateFactory
		 */
		protected $crateFactory;

		/**
		 * @param ICrateFactory $crateFactory
		 */
		public function lazyCrateFactory(ICrateFactory $crateFactory) {
			$this->crateFactory = $crateFactory;
		}

		/**
		 * @inheritdoc
		 * @throws SchemaException
		 */
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

		/**
		 * @inheritdoc
		 */
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

		/**
		 * @inheritdoc
		 * @throws EmptyResultException
		 */
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
			$crate->link($link->getName(), $link = $this->load($targetSchemaName, $selectQuery));
			return $link;
		}

		/**
		 * @inheritdoc
		 * @throws EmptyResultException
		 */
		public function load(string $crate, IQuery $query, string $schema = null): ICrate {
			/** @noinspection LoopWhichDoesNotLoopInspection */
			foreach ($this->collection($crate, $query, $schema) as $item) {
				return $item;
			}
			throw new EmptyResultException(sprintf('Cannot retrieve any crate [%s] by the given query.', $crate));
		}
	}

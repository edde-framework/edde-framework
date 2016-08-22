<?php
	declare(strict_types = 1);

	namespace Edde\Common\Crate;

	use Edde\Api\Container\IContainer;
	use Edde\Api\Crate\CrateException;
	use Edde\Api\Crate\ICollection;
	use Edde\Api\Crate\ICrate;
	use Edde\Api\Crate\ICrateFactory;
	use Edde\Api\Crate\ICrateGenerator;
	use Edde\Api\Schema\ISchemaManager;
	use Edde\Common\Usable\AbstractUsable;

	class CrateFactory extends AbstractUsable implements ICrateFactory {
		/**
		 * @var IContainer
		 */
		protected $container;
		/**
		 * @var ISchemaManager
		 */
		protected $schemaManager;
		/**
		 * @var ICrateGenerator
		 */
		protected $crateGenerator;

		/**
		 * @param IContainer $container
		 * @param ISchemaManager $schemaManager
		 * @param ICrateGenerator $crateGenerator
		 */
		public function __construct(IContainer $container, ISchemaManager $schemaManager, ICrateGenerator $crateGenerator) {
			$this->container = $container;
			$this->schemaManager = $schemaManager;
			$this->crateGenerator = $crateGenerator;
		}

		public function build(array $crateList): array {
			$this->use();
			$crates = [];
			foreach ($crateList as $schema => $source) {
				$this->load($crates[] = $crate = $this->crate($this->container->has($schema) ? $schema : Crate::class, $schema, null), $source);
			}
			return $crates;
		}

		protected function load(ICrate $crate, array $source) {
			$schema = $crate->getSchema();
			foreach ($source as $property => $value) {
				if ($schema->hasCollection($property)) {
					$schemaCollection = $schema->getCollection($property);
					$targetSchema = $schemaCollection->getTarget()
						->getSchema()
						->getSchemaName();
					$targetCrate = $this->container->has($targetSchema) ? $targetSchema : Crate::class;
					$crate->collection($property, $collection = $this->collection($targetSchema));
					/** @var $value array */
					foreach ($value as $collectionValue) {
						if (is_array($collectionValue) === false) {
							throw new CrateException(sprintf('Cannot push source value into the crate [%s]; value [%s] is not an array (collection).', $schema->getSchemaName(), $property));
						}
						$collection->addCrate($this->crate($targetCrate, $targetSchema, $collectionValue));
					}
					unset($source[$property]);
				} else if (is_array($value) && $schema->hasLink($property)) {
					$targetSchema = $schema->getLink($property)
						->getTarget()
						->getSchema()
						->getSchemaName();
					$targetCrate = $this->container->has($targetSchema) ? $targetSchema : Crate::class;
					$crate->link($property, $this->crate($targetCrate, $targetSchema, $value));
					unset($source[$property]);
				}
			}
			$crate->push($source);
			return $crate;
		}

		public function collection(string $schema, string $crate = null): ICollection {
			$this->use();
			return $this->container->create(Collection::class, $schema, $crate);
		}

		public function crate(string $crate, string $schema = null, array $load = null): ICrate {
			$this->use();
			/** @var $crate ICrate */
			$crate = $this->container->create($crate);
			$crate->setSchema($schema = $this->schemaManager->getSchema($schema ?: get_class($crate)));
			foreach ($schema->getPropertyList() as $schemaProperty) {
				$crate->addProperty(new Property($schemaProperty));
			}
			if ($load !== null) {
				$this->load($crate, $load);
			}
			return $crate;
		}

		public function include (): ICrateFactory {
			$this->crateGenerator->include();
			return $this;
		}

		protected function prepare() {
			$this->crateGenerator->generate();
		}
	}

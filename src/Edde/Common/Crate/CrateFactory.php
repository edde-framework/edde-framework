<?php
	declare(strict_types = 1);

	namespace Edde\Common\Crate;

	use Edde\Api\Container\IContainer;
	use Edde\Api\Crate\ICrate;
	use Edde\Api\Crate\ICrateFactory;
	use Edde\Api\Schema\ISchemaManager;
	use Edde\Common\AbstractObject;

	class CrateFactory extends AbstractObject implements ICrateFactory {
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
		 * @param ISchemaManager $schemaManager
		 */
		public function __construct(IContainer $container, ISchemaManager $schemaManager) {
			$this->container = $container;
			$this->schemaManager = $schemaManager;
		}

		public function createList(array $sourceList, string $name): array {
			$crateList = [];
			foreach ($sourceList as $source) {
				$crateList[] = $this->crate($source, $name);
			}
			return $crateList;
		}

		public function crate(array $source, string $name): ICrate {
			$schema = $this->schemaManager->getSchema($name);
			/** @var $crate ICrate */
			$crate = $this->container->create($schema->getSchemaName());
			$crate->setSchema($schema);
			$crate->push($source);
			return $crate;
		}

		public function build(array $crateList) {
			$crates = [];
			foreach ($crateList as $schema => $source) {
				$crates[] = $this->crate($source, $schema);
			}
			return $crates;
		}
	}

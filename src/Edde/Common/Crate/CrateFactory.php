<?php
	declare(strict_types = 1);

	namespace Edde\Common\Crate;

	use Edde\Api\Container\IContainer;
	use Edde\Api\Crate\ICollection;
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

		public function collection(string $schema, string $crate = null): ICollection {
			return $this->container->create(Collection::class, $schema, $crate);
		}

		public function build(array $crateList) {
			$crates = [];
			foreach ($crateList as $schema => $source) {
				$crates[] = $this->crate($schema, $source);
			}
			return $crates;
		}

		public function crate(string $crate, array $push = null, string $schema = null): ICrate {
			/** @var $crate ICrate */
			$crate = $this->container->create($crate);
			$crate->setSchema($this->schemaManager->getSchema($schema ?: get_class($crate)));
			if ($push !== null) {
				$crate->push($push);
			}
			return $crate;
		}
	}

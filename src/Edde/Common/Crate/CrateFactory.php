<?php
	declare(strict_types = 1);

	namespace Edde\Common\Crate;

	use Edde\Api\Container\IContainer;
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

		public function collection(string $schema, string $crate = null): ICollection {
			$this->use();
			return $this->container->create(Collection::class, $schema, $crate);
		}

		public function build(array $crateList): array {
			$this->use();
			$crates = [];
			foreach ($crateList as $schema => $source) {
				$crates[] = $this->crate($schema, $source);
			}
			return $crates;
		}

		public function crate(string $crate, array $push = null, string $schema = null): ICrate {
			$this->use();
			/** @var $crate ICrate */
			$crate = $this->container->create($crate);
			$crate->setSchema($this->schemaManager->getSchema($schema ?: get_class($crate)));
			if ($push !== null) {
				$crate->push($push);
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

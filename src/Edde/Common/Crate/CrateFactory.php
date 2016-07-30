<?php
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

		public function build(array $crateList) {
			$crates = [];
			foreach ($crateList as $schema => $source) {
				$schema = $this->schemaManager->getSchema($schema);
				if ($this->container->has($class = $schema->getSchemaName()) === false) {
					$class = Crate::class;
				}
				/** @var $crate ICrate */
				$crate = $this->container->create($class);
				$crate->setSchema($schema);
				$crate->push($source);
				$crates[] = $crate;
			}
			return $crates;
		}
	}

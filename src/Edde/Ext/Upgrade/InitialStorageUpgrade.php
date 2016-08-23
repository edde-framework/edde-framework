<?php
	declare(strict_types = 1);

	namespace Edde\Ext\Upgrade;

	use Edde\Api\Schema\ISchemaManager;
	use Edde\Api\Storage\IStorage;
	use Edde\Common\Container\LazyInjectTrait;
	use Edde\Common\Query\Schema\CreateSchemaQuery;
	use Edde\Common\Upgrade\AbstractUpgrade;

	/**
	 * This upgrade is useful for initial storage setup; it will create all available schemas.
	 */
	class InitialStorageUpgrade extends AbstractUpgrade {
		use LazyInjectTrait;

		/**
		 * @var IStorage
		 */
		protected $storage;
		/**
		 * @var ISchemaManager
		 */
		protected $schemaManager;

		/**
		 * @param string $version
		 */
		public function __construct($version = 'edde-0.5') {
			parent::__construct($version);
		}

		public function lazyStorage(IStorage $storage) {
			$this->storage = $storage;
		}

		public function lazySchemaManager(ISchemaManager $schemaManager) {
			$this->schemaManager = $schemaManager;
		}

		protected function onUpgrade() {
			$this->storage->start();
			try {
				foreach ($this->schemaManager->getSchemaList() as $schema) {
					$this->storage->execute(new CreateSchemaQuery($schema));
				}
				$this->storage->commit();
			} catch (\Exception $e) {
				$this->storage->rollback();
				throw $e;
			}
		}

		protected function prepare() {
		}
	}

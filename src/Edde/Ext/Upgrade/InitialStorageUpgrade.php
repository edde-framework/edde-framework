<?php
	namespace Edde\Ext\Upgrade;

	use Edde\Api\Schema\ISchemaManager;
	use Edde\Api\Storage\IStorage;
	use Edde\Common\Query\Schema\CreateSchemaQuery;
	use Edde\Common\Upgrade\AbstractUpgrade;

	/**
	 * This upgrade is useful for initial storage setup; it will create all available schemas.
	 */
	class InitialStorageUpgrade extends AbstractUpgrade {
		/**
		 * @var IStorage
		 */
		protected $storage;
		/**
		 * @var ISchemaManager
		 */
		protected $schemaManager;

		/**
		 * @param IStorage $storage
		 * @param ISchemaManager $schemaManager
		 * @param string $version
		 */
		public function __construct(IStorage $storage, ISchemaManager $schemaManager, $version) {
			parent::__construct($version);
			$this->storage = $storage;
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

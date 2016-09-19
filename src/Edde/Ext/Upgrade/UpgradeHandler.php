<?php
	declare(strict_types = 1);

	namespace Edde\Ext\Upgrade;

	use Edde\Api\Container\ILazyInject;
	use Edde\Api\Crate\ICrateFactory;
	use Edde\Api\Schema\ISchemaManager;
	use Edde\Api\Storage\IStorage;
	use Edde\Common\AbstractObject;
	use Edde\Common\Query\Schema\CreateSchemaQuery;
	use Edde\Common\Query\Select\SelectQuery;
	use Edde\Common\Storage\UnknownSourceException;
	use Edde\Common\Upgrade\Event\OnUpgradeEvent;
	use Edde\Common\Upgrade\Event\UpgradeStartEvent;
	use Edde\Ext\Upgrade\UpgradeStorable;

	/**
	 * Upgrade Event Handler.
	 */
	class UpgradeHandler extends AbstractObject implements ILazyInject {
		/**
		 * @var IStorage
		 */
		protected $storage;
		/**
		 * @var ISchemaManager
		 */
		protected $schemaManager;
		/**
		 * @var ICrateFactory
		 */
		protected $crateFactory;

		public function lazyStorage(IStorage $storage) {
			$this->storage = $storage;
		}

		public function lazySchemaManager(ISchemaManager $schemaManager) {
			$this->schemaManager = $schemaManager;
		}

		public function lazyCrateFactory(ICrateFactory $crateFactory) {
			$this->crateFactory = $crateFactory;
		}

		public function eventUpgradeStart(UpgradeStartEvent $upgradeStartEvent) {
			$selectQuery = new SelectQuery();
			$this->storage->start();
			$selectQuery->select()
				->all()
				->from()
				->source(UpgradeStorable::class)
				->order()
				->desc()
				->property('stamp');
			try {
				/** @var $upgradeStorable UpgradeStorable */
				$upgradeStorable = $this->storage->load(UpgradeStorable::class, $selectQuery);
				$upgradeStartEvent->getUpgradeManager()
					->setCurrentVersion($upgradeStorable->getVersion());
			} catch (UnknownSourceException $exception) {
				$this->storage->execute(new CreateSchemaQuery($this->schemaManager->getSchema(UpgradeStorable::class)));
			}
			$this->storage->commit();
		}

		public function eventOnUpgrade(OnUpgradeEvent $onUpgradeEvent) {
			$upgradeStorable = $this->crateFactory->crate(UpgradeStorable::class);
			$upgradeStorable->setStamp(microtime(true));
			$upgradeStorable->setVersion($onUpgradeEvent->getUpgrade()
				->getVersion());
			$this->storage->store($upgradeStorable);
		}
	}
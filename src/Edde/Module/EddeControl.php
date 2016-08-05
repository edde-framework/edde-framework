<?php
	declare(strict_types = 1);

	namespace Edde\Module;

	use Edde\Api\Cache\ICacheStorage;
	use Edde\Api\Control\Html\IHtmlControl;
	use Edde\Api\Crate\ICrateGenerator;
	use Edde\Api\EddeException;
	use Edde\Api\Resource\IResourceIndex;
	use Edde\Api\Upgrade\IUpgradeManager;
	use Edde\Common\Container\LazyInjectTrait;
	use Edde\Common\Html\ButtonControl;
	use Edde\Common\Html\DivControl;
	use Edde\Ext\Html\EddePageControl;
	use Tracy\Debugger;

	class EddeControl extends EddePageControl {
		use LazyInjectTrait;
		/**
		 * @var IUpgradeManager
		 */
		protected $upgradeManager;
		/**
		 * @var IResourceIndex
		 */
		protected $resourceIndex;
		/**
		 * @var ICrateGenerator
		 */
		protected $crateGenerator;
		/**
		 * @var ICacheStorage
		 */
		protected $cacheStorage;
		/**
		 * @var IHtmlControl
		 */
		protected $message;

		public function lazyUpgradeManager(IUpgradeManager $upgradeManager) {
			$this->upgradeManager = $upgradeManager;
		}

		public function lazyResourceIndex(IResourceIndex $resourceIndex) {
			$this->resourceIndex = $resourceIndex;
		}

		public function lazyCrateGenerator(ICrateGenerator $crateGenerator) {
			$this->crateGenerator = $crateGenerator;
		}

		public function lazyCacheStorage(ICacheStorage $cacheStorage) {
			$this->cacheStorage = $cacheStorage;
		}

		public function actionSetup() {
			$this->usse();
			$this->setTitle('Edde Control');
			$this->addControl($content = $this->createControl(DivControl::class));
			$content->addClass('row centered');
			$content->addControl($column = $this->createControl(DivControl::class));
			$column->addClass('col col-5');
			$column->addControl($this->createControl(ButtonControl::class, 'Upgrade', static::class, 'OnUpgrade', 'run upgrades registered to this application'));
			$column->addControl($this->createControl(ButtonControl::class, 'Update Resource Index', static::class, 'OnUpdateIndex', 'update resource index; this function needs storage already setup'));
			$column->addControl($this->createControl(ButtonControl::class, 'Rebuild crates', static::class, 'OnRebuildCrates', 'update automagically generated crates'));
			$column->addControl($this->createControl(ButtonControl::class, 'Clear cache', static::class, 'OnClearCache', 'clear current cache'));
			$this->send();
		}

		public function handleOnUpgrade() {
			$this->usse();
			try {
				$upgrade = $this->upgradeManager->upgrade();
				$this->message->addClass('success')
					->setText(sprintf('application has been upgraded to version [%s]', $upgrade->getVersion()));
			} catch (EddeException $e) {
				Debugger::log($e);
				$this->message->addClass('error')
					->setText($e->getMessage());
			}
			$this->response();
		}

		public function handleOnUpdateIndex() {
			$this->usse();
			try {
				$this->resourceIndex->update();
				$this->message->addClass('success')
					->setText('resource index has been updated');
			} catch (EddeException $e) {
				Debugger::log($e);
				$this->message->addClass('error')
					->setText($e->getMessage());
			}
			$this->response();
		}

		public function handleOnRebuildCrates() {
			$this->usse();
			try {
				$this->crateGenerator->generate();
				$this->message->addClass('success')
					->setText('crates has been rebuilt');
			} catch (EddeException $e) {
				Debugger::log($e);
				$this->message->addClass('error')
					->setText($e->getMessage());
			}
			$this->response();
		}

		public function handleOnClearCache() {
			$this->usse();
			try {
				$this->cacheStorage->invalidate();
				$this->message->addClass('success')
					->setText('cache has been wiped out');
			} catch (EddeException $e) {
				Debugger::log($e);
				$this->message->addClass('error')
					->setText($e->getMessage());
			}
			$this->response();
		}

		protected function prepare() {
			parent::prepare();
			$this->addControl($this->message = $this->createControl(DivControl::class)
				->addClass('alert')
				->setId('global-message'));
		}
	}

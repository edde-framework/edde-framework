<?php
	declare(strict_types = 1);

	namespace Edde\Module;

	use Edde\Api\Cache\ICacheStorage;
	use Edde\Api\Crate\ICrateGenerator;
	use Edde\Api\EddeException;
	use Edde\Api\Html\IHtmlControl;
	use Edde\Api\Upgrade\IUpgradeManager;
	use Edde\Common\Container\LazyInjectTrait;
	use Edde\Common\Html\DivControl;
	use Edde\Ext\Html\EddeViewControl;
	use Tracy\Debugger;

	class EddeControl extends EddeViewControl {
		use LazyInjectTrait;
		/**
		 * @var IUpgradeManager
		 */
		protected $upgradeManager;
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

		public function lazyCrateGenerator(ICrateGenerator $crateGenerator) {
			$this->crateGenerator = $crateGenerator;
		}

		public function lazyCacheStorage(ICacheStorage $cacheStorage) {
			$this->cacheStorage = $cacheStorage;
		}

		public function actionSetup() {
			$this->use();
			$this->template();
			$this->response();
		}

		public function handleOnUpgrade() {
			$this->use();
			try {
				$upgrade = $this->upgradeManager->upgrade();
				$this->message->addClass('success')
					->setText(sprintf('application has been upgraded to version [%s]', $upgrade->getVersion()));
			} catch (EddeException $e) {
				Debugger::log($e);
				$this->message->addClass('error')
					->setText($e->getMessage());
			}
			$this->ajax();
		}

		public function handleOnRebuildCrates() {
			$this->use();
			try {
				$this->crateGenerator->generate(true);
				$this->message->addClass('success')
					->setText('crates has been rebuilt');
			} catch (EddeException $e) {
				Debugger::log($e);
				$this->message->addClass('error')
					->setText($e->getMessage());
			}
			$this->ajax();
		}

		public function handleOnClearCache() {
			$this->use();
			try {
				$this->cacheStorage->invalidate();
				$this->message->addClass('success')
					->setText('cache has been wiped out');
			} catch (EddeException $e) {
				Debugger::log($e);
				$this->message->addClass('error')
					->setText($e->getMessage());
			}
			$this->ajax();
		}

		protected function prepare() {
			parent::prepare();
			$this->addStyleSheet(__DIR__ . '/assets/css/kube.css');
			$this->addControl($this->message = $this->createControl(DivControl::class)
				->addClass('alert')
				->setId('global-message'));
		}
	}

<?php
	declare(strict_types = 1);

	namespace Edde\Module;

	use Edde\Api\Cache\ICacheStorage;
	use Edde\Api\Crate\ICrateGenerator;
	use Edde\Api\EddeException;
	use Edde\Api\Html\IHtmlControl;
	use Edde\Api\Upgrade\IUpgradeManager;
	use Edde\Common\Container\LazyInjectTrait;
	use Edde\Common\Html\TemplateViewControl;
	use Tracy\Debugger;

	class EddeControl extends TemplateViewControl {
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

		public function handleOnUpgrade() {
			$this->use();
			try {
				$this->template(__DIR__ . '/template/action-setup.xml');
				$this->message('success', sprintf('application has been upgraded to version [%s]', $this->upgradeManager->upgrade()
					->getVersion()));
			} catch (EddeException $e) {
				Debugger::log($e);
				$this->message('error', $e->getMessage());
			}
			$this->response();
		}

		protected function message(string $class, string $message) {
			$this->message->addClass($class)
				->setText($message)
				->dirty();
		}

		public function handleOnRebuildCrates() {
			$this->use();
			try {
				$this->template(__DIR__ . '/template/action-setup.xml');
				$this->crateGenerator->generate(true);
				$this->message('success', 'crates has been rebuilt');
			} catch (EddeException $e) {
				Debugger::log($e);
				$this->message('error', $e->getMessage());
			}
			$this->response();
		}

		public function handleOnClearCache() {
			$this->use();
			try {
				$this->template(__DIR__ . '/template/action-setup.xml');
				$this->cacheStorage->invalidate();
				$this->message('success', 'cache has been wiped out');
			} catch (EddeException $e) {
				Debugger::log($e);
				$this->message('error', $e->getMessage());
			}
			$this->response();
		}
	}

<?php
	declare(strict_types = 1);

	namespace Edde\Module;

	use Edde\Api\Cache\ICacheStorage;
	use Edde\Api\Crate\ICrateGenerator;
	use Edde\Api\EddeException;
	use Edde\Api\Html\IHtmlControl;
	use Edde\Api\Upgrade\IUpgradeManager;
	use Edde\Common\Html\TemplateViewControl;
	use Edde\Framework;
	use Tracy\Debugger;

	class EddeControl extends TemplateViewControl {
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
		 * @var Framework
		 */
		protected $framework;
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

		public function lazyFramework(Framework $framework) {
			$this->framework = $framework;
		}

		public function getVersion() {
			return $this->framework->getVersionString();
		}

		public function handleOnUpgrade() {
			$this->use();
			try {
				$this->snippet(__DIR__ . '/template/layout.xml', 'message');
				$this->message('success', sprintf('application has been upgraded to version [%s]', $this->upgradeManager->upgrade()
					->getVersion()));
			} catch (EddeException $e) {
				Debugger::log($e);
				$this->message('alert', $e->getMessage());
			}
			$this->response();
		}

		protected function message(string $class, string $message) {
			$this->message->javascript('message', __DIR__ . '/template/js/message.js')
				->stylesheet(__DIR__ . '/template/css/message.css')
				->addClass($class)
				->setText($message)
				->dirty();
		}

		public function handleOnRebuildCrates() {
			$this->use();
			try {
				$this->snippet(__DIR__ . '/template/layout.xml', 'message');
				$this->crateGenerator->generate(true);
				$this->message('success', 'crates has been rebuilt');
			} catch (EddeException $e) {
				Debugger::log($e);
				$this->message('alert', $e->getMessage());
			}
			$this->response();
		}

		public function handleOnClearCache() {
			$this->use();
			try {
				$this->snippet(__DIR__ . '/template/layout.xml', 'message');
				$this->cacheStorage->invalidate();
				$this->message('success', 'cache has been wiped out');
			} catch (EddeException $e) {
				Debugger::log($e);
				$this->message('alert', $e->getMessage());
			}
			$this->response();
		}
	}

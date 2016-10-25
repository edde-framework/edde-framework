<?php
	declare(strict_types = 1);

	namespace Edde\Module;

	use Edde\Api\Cache\LazyCacheStorageTrait;
	use Edde\Api\Crate\LazyCrateGeneratorTrait;
	use Edde\Api\EddeException;
	use Edde\Api\Html\IHtmlControl;
	use Edde\Api\Log\LazyLogServiceTrait;
	use Edde\Api\Upgrade\LazyUpgradeManagerTrait;
	use Edde\Common\Html\ViewControl;
	use Edde\LazyFrameworkTrait;

	class EddeControl extends ViewControl {
		use LazyCrateGeneratorTrait;
		use LazyUpgradeManagerTrait;
		use LazyCacheStorageTrait;
		use LazyLogServiceTrait;
		use LazyFrameworkTrait;
		/**
		 * @var IHtmlControl
		 */
		protected $message;

		/**
		 * template method for getting a framework version
		 *
		 * @return string
		 */
		public function getVersion() {
			return $this->framework->getVersionString();
		}

		public function contextSetup() {
			$this->use();
			$this->template();
		}

		public function handleSetup() {
			$this->response();
		}

		public function contextMessage() {
			$this->use();
			$this->template(['message']);
		}

		public function handleOnUpgrade() {
			$this->use();
			try {
				$this->message('success', sprintf('application has been upgraded to version [%s]', $this->upgradeManager->upgrade()
					->getVersion()));
			} catch (EddeException $e) {
				$this->logService->exception($e, [
					'edde',
				]);
				$this->message('alert', $e->getMessage());
			}
			$this->response();
		}

		protected function message(string $class, string $message) {
			/** @noinspection PhpDeprecationInspection */
			$this->message->javascript('message', __DIR__ . '/template/js/message.js')
				->stylesheet(__DIR__ . '/template/css/message.css')
				->addClass($class)
				->setText($message)
				->dirty();
		}

		public function handleOnRebuildCrates() {
			try {
				$this->crateGenerator->generate(true);
				$this->message('success', 'crates has been rebuilt');
			} catch (EddeException $e) {
				$this->logService->exception($e, [
					'edde',
				]);
				$this->message('alert', $e->getMessage());
			}
			$this->response();
		}

		public function handleOnClearCache() {
			try {
				$this->cacheStorage->invalidate();
				$this->message('success', 'cache has been wiped out');
			} catch (EddeException $e) {
				$this->logService->exception($e, [
					'edde',
				]);
				$this->message('alert', $e->getMessage());
			}
			$this->response();
		}
	}

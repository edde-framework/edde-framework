<?php
	declare(strict_types = 1);

	namespace Edde\Module;

	use Edde\Api\Cache\LazyCacheStorageTrait;
	use Edde\Api\Crate\LazyCrateGeneratorTrait;
	use Edde\Api\EddeException;
	use Edde\Api\Html\IHtmlControl;
	use Edde\Api\Upgrade\LazyUpgradeManagerTrait;
	use Edde\Common\Html\TemplateViewControl;
	use Edde\Framework;
	use Tracy\Debugger;

	class EddeControl extends TemplateViewControl {
		use LazyCrateGeneratorTrait;
		use LazyUpgradeManagerTrait;
		use LazyCacheStorageTrait;
		/**
		 * @var Framework
		 */
		protected $framework;
		/**
		 * @var IHtmlControl
		 */
		protected $message;

		/**
		 * @param Framework $framework
		 */
		public function lazyFramework(Framework $framework) {
			$this->framework = $framework;
		}

		/**
		 * template method for getting a framework version
		 *
		 * @return string
		 */
		public function getVersion() {
			return $this->framework->getVersionString();
		}

		public function handleOnUpgrade() {
			$this->use();
			try {
				$this->template(null, ['message']);
				$this->message('success', sprintf('application has been upgraded to version [%s]', $this->upgradeManager->upgrade()
					->getVersion()));
			} catch (EddeException $e) {
				Debugger::log($e);
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
			$this->use();
			try {
				$this->template(null, ['message']);
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
				$this->template(null, ['message']);
				$this->cacheStorage->invalidate();
				$this->message('success', 'cache has been wiped out');
			} catch (EddeException $e) {
				Debugger::log($e);
				$this->message('alert', $e->getMessage());
			}
			$this->response();
		}
	}

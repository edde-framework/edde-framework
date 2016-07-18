<?php
	namespace Edde\Module;

	use Edde\Api\Control\Html\IHtmlControl;
	use Edde\Api\EddeException;
	use Edde\Api\Resource\IResourceIndex;
	use Edde\Api\Upgrade\IUpgradeManager;
	use Edde\Common\Container\LazyInjectTrait;
	use Edde\Common\Control\Html\HtmlControl;
	use Edde\Common\Resource\FileResource;

	class EddeControl extends HtmlControl {
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
		 * @var IHtmlControl
		 */
		protected $message;

		final public function lazyUpgradeManager(IUpgradeManager $upgradeManager) {
			$this->upgradeManager = $upgradeManager;
		}

		final public function lazyResourceIndex(IResourceIndex $resourceIndex) {
			$this->resourceIndex = $resourceIndex;
		}

		public function actionSetup() {
			$this->usse();
			$this->setTitle('Edde Control');
			$this->addStyleSheet(new FileResource(__DIR__ . '/assets/css/kube.css'));
			$this->addJavaScript(new FileResource(__DIR__ . '/assets/js/jquery-3.1.0.js'));
			$this->addJavaScript(new FileResource(__DIR__ . '/assets/js/edde-framework.js'));
			$this->addControl($this->message);
			$this->addControl($content = $this->createDivControl());
			$content->addClass('row centered');
			$content->addControl($column = $this->createDivControl());
			$column->addClass('col col-4');
			$column->addControl($this->createButtonControl('Upgrade', static::class, 'OnUpgrade', 'run upgrades registered to this application'));
			$column->addControl($this->createButtonControl('Update Resource Index', static::class, 'OnUpdateIndex', 'update resource index; this function needs storage already setup'));
			$this->send();
		}

		public function handleOnUpgrade() {
			$this->usse();
			$this->addControl($this->message);
			try {
				$this->upgradeManager->upgrade();
			} catch (EddeException $e) {
				$this->message->addClass('error')
					->setText($e->getMessage());
			}
			$this->response();
		}

		public function handleOnUpdateIndex() {
			$this->usse();
			$this->addControl($this->message);
			try {
				$this->resourceIndex->update();
			} catch (EddeException $e) {
				$this->message->addClass('error')
					->setText($e->getMessage());
			}
			$this->response();
		}

		protected function onPrepare() {
			$this->message = $this->createDivControl()
				->addClass('alert')
				->setId('global-message');
		}
	}

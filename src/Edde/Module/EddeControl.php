<?php
	namespace Edde\Module;

	use Edde\Api\Control\Html\IHtmlControl;
	use Edde\Api\EddeException;
	use Edde\Api\Resource\IResourceIndex;
	use Edde\Api\Upgrade\IUpgradeManager;
	use Edde\Common\Container\LazyInjectTrait;
	use Edde\Common\Control\Html\EddeHtmlControl;

	class EddeControl extends EddeHtmlControl {
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
			$content = $this->createDivControl();
			$content->addClass('row centered');
			$column = $content->createDivControl();
			$column->addClass('col col-4');
			$column->createButtonControl('Upgrade', static::class, 'OnUpgrade', 'run upgrades registered to this application');
			$column->createButtonControl('Update Resource Index', static::class, 'OnUpdateIndex', 'update resource index; this function needs storage already setup');
			$this->send();
		}

		public function handleOnUpgrade() {
			$this->usse();
			try {
				$this->upgradeManager->upgrade();
				$this->message->addClass('success')
					->setText('application has been upgraded');
			} catch (EddeException $e) {
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
				$this->message->addClass('error')
					->setText($e->getMessage());
			}
			$this->response();
		}

		protected function prepare() {
			parent::prepare();
			$this->message = $this->createDivControl()
				->addClass('alert')
				->setId('global-message');
		}
	}

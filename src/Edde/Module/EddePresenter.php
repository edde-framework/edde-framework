<?php
	namespace Edde\Module;

	use Edde\Api\Control\Html\IHtmlControl;
	use Edde\Api\EddeException;
	use Edde\Api\Resource\IResourceIndex;
	use Edde\Api\Upgrade\IUpgradeManager;
	use Edde\Common\Container\LazyInjectTrait;
	use Edde\Common\Control\Html\HtmlPresenter;
	use Edde\Common\Resource\FileResource;
	use Edde\Common\Response\HtmlResponse;

	class EddePresenter extends HtmlPresenter {
		use LazyInjectTrait;
		/**
		 * @var IUpgradeManager
		 */
		protected $upgradeManager;
		/**
		 * @var IResourceIndex
		 */
		protected $resourceIndex;

		final public function lazyUpgradeManager(IUpgradeManager $upgradeManager) {
			$this->upgradeManager = $upgradeManager;
		}

		final public function lazyResourceIndex(IResourceIndex $resourceIndex) {
			$this->resourceIndex = $resourceIndex;
		}

		public function actionSetup() {
			$this->setTitle('Edde Control');
			$this->addStyleSheet(new FileResource(__DIR__ . '/assets/css/kube.css'));
			$this->addJavaScript(new FileResource(__DIR__ . '/assets/js/jquery-3.1.0.js'));
			$this->addJavaScript(new FileResource(__DIR__ . '/assets/js/edde-framework.js'));
			$this->addControl($this->createGlobalMessage());
			$this->addControl($content = $this->createDivControl());
			$content->addClass('row centered');
			$content->addControl($column = $this->createDivControl());
			$column->addClass('col col-4');
			$column->addControl($this->createButtonControl('Upgrade', static::class, 'OnUpgrade', 'run upgrades registered to this application'));
			$column->addControl($this->createButtonControl('Update Resource Index', static::class, 'OnUpdateIndex', 'update resource index; this function needs storage already setup'));
			$this->send();
		}

		/**
		 * @return IHtmlControl
		 */
		protected function createGlobalMessage() {
			return $this->createDivControl()
				->addClass('alert')
				->setId('global-message');
		}

		public function handleOnUpgrade() {
			$htmlResponse = new HtmlResponse();
			$globalMessage = $this->createGlobalMessage();
			try {
				$this->upgradeManager->upgrade();
			} catch (EddeException $e) {
				$htmlResponse->addControl('#' . $globalMessage->getId(), $globalMessage->addClass('error')
					->setText($e->getMessage()));
			}
			$htmlResponse->send();
		}

		public function handleOnUpdateIndex() {
			$htmlResponse = new HtmlResponse();
			$globalMessage = $this->createGlobalMessage();
			try {
				$this->resourceIndex->update();
			} catch (EddeException $e) {
				$htmlResponse->addControl('#' . $globalMessage->getId(), $globalMessage->addClass('error')
					->setText($e->getMessage()));
			}
			$htmlResponse->send();
		}
	}

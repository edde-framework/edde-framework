<?php
	namespace Edde\Module;

	use Edde\Common\Control\Html\HtmlPresenter;
	use Edde\Common\Resource\FileResource;

	class EddePresenter extends HtmlPresenter {
		public function actionSetup() {
			$this->setTitle('Edde Control');
			$this->addStyleSheet(new FileResource(__DIR__ . '/assets/css/kube.css'));
			$this->render();
		}
	}

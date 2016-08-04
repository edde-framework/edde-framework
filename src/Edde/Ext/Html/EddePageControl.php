<?php
	declare(strict_types = 1);

	namespace Edde\Ext\Html;

	use Edde\Common\Html\PageControl;
	use Edde\Common\Resource\FileResource;

	/**
	 * Same as AbstractHtmlControl extended by default set of javascript, styles and other stuff.
	 */
	class EddePageControl extends PageControl {
		protected function prepare() {
			parent::prepare();
			$this->addStyleSheet(new FileResource(__DIR__ . '/assets/css/kube.css'));
			$this->addStyleSheet(new FileResource(__DIR__ . '/assets/css/edde-framework.css'));
			$this->addJavaScript(new FileResource(__DIR__ . '/assets/js/jquery-3.1.0.js'));
			$this->addJavaScript(new FileResource(__DIR__ . '/assets/js/edde-framework.js'));
		}
	}

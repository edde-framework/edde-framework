<?php
	declare(strict_types = 1);

	namespace Edde\Ext\Html;

	use Edde\Common\Html\ViewControl;
	use Edde\Common\Resource\FileResource;

	/**
	 * Same as AbstractHtmlControl extended by default set of javascript, styles and other stuff.
	 */
	class EddeViewControl extends ViewControl {
		protected function prepare() {
			$this->addJavaScript(new FileResource(__DIR__ . '/assets/js/jquery-3.1.0.js'));
			$this->addJavaScript(new FileResource(__DIR__ . '/assets/js/edde-framework.js'));
			$this->addStyleSheet(new FileResource(__DIR__ . '/assets/css/edde-framework.css'));
		}
	}

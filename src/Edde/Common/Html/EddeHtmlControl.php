<?php
	declare(strict_types = 1);

	namespace Edde\Common\Html;

	use Edde\Common\Resource\FileResource;

	/**
	 * Same as HtmlControl extended by default set of javascript, styles and other stuff.
	 */
	class EddeHtmlControl extends HtmlControl {
		protected function prepare() {
			parent::prepare();
			$this->addStyleSheet(new FileResource(__DIR__ . '/assets/css/kube.css'));
			$this->addStyleSheet(new FileResource(__DIR__ . '/assets/css/edde-framework.css'));
			$this->addJavaScript(new FileResource(__DIR__ . '/assets/js/jquery-3.1.0.js'));
			$this->addJavaScript(new FileResource(__DIR__ . '/assets/js/edde-framework.js'));
		}
	}

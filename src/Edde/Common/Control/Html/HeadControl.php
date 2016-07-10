<?php
	namespace Edde\Common\Control\Html;

	use Edde\Api\Node\IAbstractNode;

	class HeadControl extends AbstractHtmlControl {
		/**
		 * @var TitleControl
		 */
		private $title;

		/**
		 * set the title of this head control
		 *
		 * @param string $title
		 */
		public function setTitle($title) {
			$this->usse();
			$this->title->setTitle($title);
		}

		/**
		 * add the given javascript file to this head; the src should be accessible from a client
		 *
		 * @param string $src
		 *
		 * @return $this
		 */
		public function addJavaScript($src) {
			$this->usse();
			$this->addControl((new JavaScriptControl())->setSrc($src));
			return $this;
		}

		/**
		 * add the given stylesheet; the file should be accessible from a client
		 *
		 * @param string $href
		 *
		 * @return $this
		 */
		public function addStyleSheet($href) {
			$this->usse();
			$this->addControl((new StyleSheetControl())->setHref($href));
			return $this;
		}

		public function getTag() {
			return 'head';
		}

		public function accept(IAbstractNode $abstractNode) {
			return $abstractNode instanceof MetaControl || $abstractNode instanceof LinkControl || $abstractNode instanceof TitleControl || $abstractNode instanceof JavaScriptControl;
		}

		protected function onPrepare() {
			$this->addControl((new MetaControl())->setAttribute('charset', 'utf-8'));
			$this->addControl($this->title = new TitleControl());
		}
	}

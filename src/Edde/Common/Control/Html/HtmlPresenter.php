<?php
	namespace Edde\Common\Control\Html;

	use Edde\Api\Resource\IResource;
	use Edde\Api\Resource\IResourceList;
	use Edde\Api\Web\IJavaScriptCompiler;
	use Edde\Api\Web\IStyleSheetCompiler;
	use Edde\Common\Container\LazyInjectTrait;
	use Edde\Common\Control\ControlTrait;
	use Edde\Common\Resource\ResourceList;

	/**
	 * Formal root control for displaying page.
	 */
	class HtmlPresenter extends DocumentControl {
		use LazyInjectTrait;
		use ControlTrait;

		/**
		 * @var IStyleSheetCompiler
		 */
		protected $styleSheetCompiler;
		/**
		 * @var IJavaScriptCompiler
		 */
		protected $javaScriptCompiler;
		/**
		 * @var IResourceList
		 */
		protected $styleSheetList;
		/**
		 * @var IResourceList
		 */
		protected $javaScriptList;

		final public function lazyStyleSheetCompiler(IStyleSheetCompiler $styleSheetCompiler) {
			$this->styleSheetCompiler = $styleSheetCompiler;
		}

		final public function lazyJavaScriptCompiler(IJavaScriptCompiler $javaScriptCompiler) {
			$this->javaScriptCompiler = $javaScriptCompiler;
		}

		public function setTitle($title) {
			$this->getHead()
				->setTitle($title);
		}

		public function addStyleSheet(IResource $resource) {
			$this->usse();
			$this->styleSheetList->addResource($resource);
			return $this;
		}

		public function addJavaScript(IResource $resource) {
			$this->usse();
			$this->javaScriptList->addResource($resource);
			return $this;
		}

		public function render() {
			$this->head->addStyleSheet($this->styleSheetCompiler->compile($this->styleSheetList)
				->getRelativePath());
			$this->head->addJavaScript($this->javaScriptCompiler->compile($this->javaScriptList)
				->getRelativePath());
			return parent::render();
		}

		protected function prepare() {
			parent::prepare();
			$this->styleSheetList = new ResourceList();
			$this->javaScriptList = new ResourceList();
			$this->head->addControl($this->createMetaControl()
				->setAttributeList([
					'name' => 'viewport',
					'content' => 'width=device-width, initial-scale=1',
				]));
		}
	}

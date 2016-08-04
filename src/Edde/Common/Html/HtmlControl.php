<?php
	declare(strict_types = 1);

	namespace Edde\Common\Html;

	use Edde\Api\Resource\IResource;
	use Edde\Api\Resource\IResourceList;
	use Edde\Api\Web\IJavaScriptCompiler;
	use Edde\Api\Web\IStyleSheetCompiler;
	use Edde\Common\Container\LazyInjectTrait;
	use Edde\Common\Control\ControlTrait;
	use Edde\Common\Resource\ResourceList;
	use Edde\Common\Response\HtmlResponse;

	/**
	 * Formal root control for displaying page.
	 */
	class HtmlControl extends DocumentControl {
		use LazyInjectTrait;
		use ControlTrait;
		/**
		 * @var HtmlResponse
		 */
		protected $htmlResponse;
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

		final public function lazyHtmlResponse(HtmlResponse $htmlResponse) {
			$this->htmlResponse = $htmlResponse;
		}

		final public function lazyStyleSheetCompiler(IStyleSheetCompiler $styleSheetCompiler) {
			$this->styleSheetCompiler = $styleSheetCompiler;
		}

		final public function lazyJavaScriptCompiler(IJavaScriptCompiler $javaScriptCompiler) {
			$this->javaScriptCompiler = $javaScriptCompiler;
		}

		public function setTitle($title) {
			$this->getHead()
				->setTitle($title);
			return $this;
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
			if ($this->styleSheetList->isEmpty() === false) {
				$this->head->addStyleSheet($this->styleSheetCompiler->compile($this->styleSheetList)
					->getRelativePath());
			}
			if ($this->javaScriptList->isEmpty() === false) {
				$this->head->addJavaScript($this->javaScriptCompiler->compile($this->javaScriptList)
					->getRelativePath());
			}
			return parent::render();
		}

		/**
		 * method specific for this "presenter"; this will sent a HtmlResponse with controls currently set to the body
		 *
		 * @return $this
		 */
		public function response() {
			$this->htmlResponse->setControlList($this->body->getControlList());
			$this->htmlResponse->send();
			return $this;
		}

		protected function prepare() {
			parent::prepare();
			$this->styleSheetList = new ResourceList();
			$this->javaScriptList = new ResourceList();
			$this->head->createMetaControl()
				->setAttributeList([
					'name' => 'viewport',
					'content' => 'width=device-width, initial-scale=1',
				]);
		}
	}

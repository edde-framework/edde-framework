<?php
	namespace Edde\Common\Control\Html;

	use Edde\Api\Resource\IResource;
	use Edde\Api\Resource\IResourceList;
	use Edde\Api\Web\IStyleSheetCompiler;
	use Edde\Common\Container\LazyInjectTrait;
	use Edde\Common\Resource\ResourceList;

	/**
	 * Formal root control for displaying page.
	 */
	class HtmlPresenter extends DocumentControl {
		use LazyInjectTrait;

		/**
		 * @var IStyleSheetCompiler
		 */
		protected $styleSheetCompiler;
		/**
		 * @var IResourceList
		 */
		protected $styleSheetList;

		final public function lazyStyleSheetCompiler(IStyleSheetCompiler $styleSheetCompiler) {
			$this->styleSheetCompiler = $styleSheetCompiler;
		}

		public function setTitle($title) {
			$this->getHead()
				->setTitle($title);
		}

		public function addStyleSheet(IResource $resource) {
			$this->prepare();
			$this->styleSheetList->addResource($resource);
			return $this;
		}

		protected function prepare() {
			parent::prepare();
			$this->styleSheetList = new ResourceList();
			$this->head->addControl((new MetaControl())->setAttributeList([
				'name' => 'viewport',
				'content' => 'width=device-width, initial-scale=1',
			]));
		}

		public function render() {
			$this->head->addStyleSheet($this->styleSheetCompiler->compile($this->styleSheetList)
				->getRelativePath());
			parent::render();
		}
	}

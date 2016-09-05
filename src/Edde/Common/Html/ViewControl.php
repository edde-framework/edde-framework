<?php
	declare(strict_types = 1);

	namespace Edde\Common\Html;

	use Edde\Api\Control\ControlException;
	use Edde\Api\Html\IHtmlControl;
	use Edde\Api\Html\IHtmlView;
	use Edde\Api\Http\IHttpRequest;
	use Edde\Api\Http\IHttpResponse;
	use Edde\Api\Link\ILinkFactory;
	use Edde\Api\Resource\IResource;
	use Edde\Api\Resource\IResourceList;
	use Edde\Api\Web\IJavaScriptCompiler;
	use Edde\Api\Web\IStyleSheetCompiler;
	use Edde\Common\Container\LazyInjectTrait;
	use Edde\Common\File\File;
	use Edde\Common\Html\Document\DocumentControl;
	use Edde\Common\Html\Document\MetaControl;
	use Edde\Common\Response\AjaxResponse;
	use Edde\Common\Response\HtmlResponse;

	/**
	 * Formal root control for displaying page with some shorthands.
	 */
	class ViewControl extends DocumentControl implements IHtmlView {
		use LazyInjectTrait;
		use TemplateTrait;
		/**
		 * @var IHttpRequest
		 */
		protected $httpRequest;
		/**
		 * @var IHttpResponse
		 */
		protected $httpResponse;
		/**
		 * @var IStyleSheetCompiler
		 */
		protected $styleSheetCompiler;
		/**
		 * @var IJavaScriptCompiler
		 */
		protected $javaScriptCompiler;
		/**
		 * @var ILinkFactory
		 */
		protected $linkFactory;
		/**
		 * @var IResourceList
		 */
		protected $styleSheetList;
		/**
		 * @var IResourceList
		 */
		protected $javaScriptList;
		/**
		 * @var array
		 */
		protected $snippetList = [];
		/**
		 * @var IHtmlControl[]
		 */
		protected $snippets;

		public function lazyHttpRequest(IHttpRequest $httpRequest) {
			$this->httpRequest = $httpRequest;
		}

		public function lazyHttpResponse(IHttpResponse $httpResponse) {
			$this->httpResponse = $httpResponse;
		}

		public function lazyStyleSheetCompiler(IStyleSheetCompiler $styleSheetCompiler) {
			$this->styleSheetCompiler = $styleSheetCompiler;
		}

		public function lazyJavaScriptCompiler(IJavaScriptCompiler $javaScriptCompiler) {
			$this->javaScriptCompiler = $javaScriptCompiler;
		}

		public function lazyLinkFactory(ILinkFactory $linkFactory) {
			$this->linkFactory = $linkFactory;
		}

		public function setAttribute($attribute, $value) {
			switch ($attribute) {
				case 'title':
					$this->setTitle($value);
					break;
				default:
					parent::setAttribute($attribute, $value);
			}
			return $this;
		}

		public function setTitle($title) {
			$this->getHead()
				->setTitle($title);
			return $this;
		}

		public function addStyleSheet(string $file) {
			$this->use();
			$this->styleSheetList->addResource(new File($file));
			return $this;
		}

		public function addStyleSheetResource(IResource $resource) {
			$this->use();
			$this->styleSheetList->addResource($resource);
			return $this;
		}

		public function addJavaScript(string $file) {
			$this->use();
			$this->javaScriptList->addResource(new File($file));
			return $this;
		}

		public function addJavaScriptResource(IResource $resource) {
			$this->use();
			$this->javaScriptList->addResource($resource);
			return $this;
		}

		/**
		 * response redirect response to the client
		 *
		 * @param mixed $redirect
		 *
		 * @return $this
		 */
		public function redirect($redirect) {
			$this->use();
			$link = $this->linkFactory->generate($redirect);
			if ($this->httpRequest->isAjax()) {
				(new AjaxResponse($this->httpResponse))->redirect($link)
					->render();
				return $this;
			}
			$this->httpResponse->redirect($link)
				->render();
			return $this;
		}

		public function response(): IHtmlView {
			$this->use();
			if ($this->httpRequest->isAjax()) {
				return $this->ajax();
			}
			(new HtmlResponse($this->httpResponse))->render(function () {
				return $this->render();
			});
			return $this;
		}

		/**
		 * method specific for this "presenter"; this will sent a AjaxResponse with controls currently set to the body
		 *
		 * @return IHtmlView
		 * @throws ControlException
		 */
		public function ajax(): IHtmlView {
			$this->use();
			$ajax = new AjaxResponse($this->httpResponse);
			if ($this->javaScriptCompiler->isEmpty() === false) {
				$ajax->setJavaScriptList([
					$this->javaScriptCompiler->compile($this->javaScriptCompiler)
						->getRelativePath(),
				]);
			}
			if ($this->styleSheetCompiler->isEmpty() === false) {
				$ajax->setStyleSheetList([
					$this->styleSheetCompiler->compile($this->styleSheetCompiler)
						->getRelativePath(),
				]);
			}
			/** @var $control IHtmlControl */
			foreach ($this as $control) {
				if ($control->isDirty() && $control->getId() !== null) {
					$ajax->replace($control);
				}
			}
			foreach ($this->snippets() as $snippet) {
				$ajax->replace($snippet);
			}
			$ajax->render();
			return $this;
		}

		public function snippets($force = false): array {
			if ($this->snippets !== null && $force === false) {
				return $this->snippets;
			}
			$this->snippets = [];
			foreach ($this->snippetList as $snippet) {
				/** @var $htmlControl IHtmlControl */
				list($htmlControl, $callback) = $snippet;
				$callback ? $callback($htmlControl) : null;
				if ($htmlControl->isDirty()) {
					$this->snippets[] = $htmlControl;
				}
			}
			return $this->snippets;
		}

		public function render() {
			$this->use();
			if ($this->styleSheetList->isEmpty() === false) {
				$this->head->addStyleSheet($this->styleSheetCompiler->compile($this->styleSheetList)
					->getRelativePath());
			}
			if ($this->javaScriptList->isEmpty() === false) {
				$this->head->addJavaScript($this->javaScriptCompiler->compile($this->javaScriptList)
					->getRelativePath());
			}
			$this->dirty();
			return parent::render();
		}

		public function snippet(IHtmlControl $htmlControl, callable $callback = null): IHtmlView {
			$this->snippetList[] = [
				$htmlControl,
				$callback,
			];
			return $this;
		}

		protected function prepare() {
			parent::prepare();
			$this->styleSheetList = $this->styleSheetCompiler;
			$this->javaScriptList = $this->javaScriptCompiler;
			$this->head->addControl($this->createControl(MetaControl::class)
				->setAttributeList([
					'name' => 'viewport',
					'content' => 'width=device-width, initial-scale=1',
				]));
		}
	}

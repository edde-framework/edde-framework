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
	use Edde\Common\Response\AbstractResponse;

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
				$this->httpResponse->setResponse(new class($redirect) extends AbstractResponse {
					protected $redirect;

					public function __construct($link) {
						$this->redirect = $link;
					}

					public function render(): string {
						return json_encode(['redirect' => $this->redirect]);
					}
				});
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
			$this->httpResponse->setResponse(new class($this) extends AbstractResponse {
				/**
				 * @var IHtmlControl
				 */
				protected $htmlControl;

				public function __construct(IHtmlControl $htmlControl) {
					$this->htmlControl = $htmlControl;
				}

				public function render(): string {
					return $this->htmlControl->render();
				}
			});
			return $this;
		}

		/**
		 * method specific for this "presenter"; this will sent a proprietary ajax response
		 *
		 * @return IHtmlView
		 * @throws ControlException
		 */
		public function ajax(): IHtmlView {
			$this->use();
			$this->httpResponse->contentType('application/json')
				->setResponse(new class($this, $this->javaScriptCompiler, $this->styleSheetCompiler) extends AbstractResponse {
					/**
					 * @var ViewControl
					 */
					protected $viewControl;
					/**
					 * @var IJavaScriptCompiler
					 */
					protected $javaScriptCompiler;
					/**
					 * @var IStyleSheetCompiler
					 */
					protected $styleSheetCompiler;

					/**
					 * @param ViewControl $viewControl
					 * @param IJavaScriptCompiler $javaScriptCompiler
					 * @param IStyleSheetCompiler $styleSheetCompiler
					 */
					public function __construct(ViewControl $viewControl, IJavaScriptCompiler $javaScriptCompiler, IStyleSheetCompiler $styleSheetCompiler) {
						$this->viewControl = $viewControl;
						$this->javaScriptCompiler = $javaScriptCompiler;
						$this->styleSheetCompiler = $styleSheetCompiler;
					}

					public function render(): string {
						$ajax = [];
						if ($this->javaScriptCompiler->isEmpty() === false) {
							$ajax['javaScript'] = [
								$this->javaScriptCompiler->compile($this->javaScriptCompiler)
									->getRelativePath(),
							];
						}
						if ($this->styleSheetCompiler->isEmpty() === false) {
							$ajax['styleSheet'] = [
								$this->styleSheetCompiler->compile($this->styleSheetCompiler)
									->getRelativePath(),
							];
						}
						foreach ($this->viewControl->invalidate() as $control) {
							if (($id = $control->getId()) !== '') {
								$ajax['selector']['#' . $id] = [
									'action' => 'replace',
									'source' => $control->render(),
								];
							}
						}
						return json_encode($ajax);
					}
				});
			return $this;
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

		public function handle(string $method, array $parameterList, array $crateList) {
			$result = parent::handle($method, $parameterList, $crateList);
			$this->httpResponse->render();
			return $result;
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

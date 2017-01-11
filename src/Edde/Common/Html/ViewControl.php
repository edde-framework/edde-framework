<?php
	declare(strict_types = 1);

	namespace Edde\Common\Html;

	use Edde\Api\Application\LazyResponseManagerTrait;
	use Edde\Api\Html\IHtmlView;
	use Edde\Api\Http\LazyHttpRequestTrait;
	use Edde\Api\Link\LazyLinkFactoryTrait;
	use Edde\Api\Resource\IResource;
	use Edde\Common\File\File;
	use Edde\Common\Html\Document\DocumentControl;
	use Edde\Common\Html\Document\MetaControl;

	/**
	 * Formal root control for displaying page with some shorthands.
	 */
	class ViewControl extends DocumentControl implements IHtmlView {
		use LazyHttpRequestTrait;
		use LazyLinkFactoryTrait;
		use LazyResponseManagerTrait;
		use TemplateTrait;
		use ResponseTrait;
		use RedirectTrait;

		/**
		 * @inheritdoc
		 */
		public function setAttribute($attribute, $value) {
			/** @noinspection DegradedSwitchInspection */
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
			$this->head->setTitle($title);
			return $this;
		}

		public function addStyleSheet(string $file) {
			$this->styleSheetCompiler->addResource(new File($file));
			return $this;
		}

		public function addStyleSheetResource(IResource $resource) {
			$this->styleSheetCompiler->addResource($resource);
			return $this;
		}

		public function addJavaScript(string $file) {
			$this->javaScriptCompiler->addResource(new File($file));
			return $this;
		}

		public function addJavaScriptResource(IResource $resource) {
			$this->javaScriptCompiler->addResource($resource);
			return $this;
		}

		/**
		 * @inheritdoc
		 */
		public function render(int $indent = 0): string {
			if ($this->styleSheetCompiler->isEmpty() === false) {
				$this->head->addStyleSheet($this->styleSheetCompiler->compile()
					->getRelativePath());
			}
			if ($this->javaScriptCompiler->isEmpty() === false) {
				$this->head->addJavaScript($this->javaScriptCompiler->compile()
					->getRelativePath());
			}
			$this->dirty();
			return parent::render($indent);
		}

		/**
		 * @inheritdoc
		 */
		protected function handleInit() {
			parent::handleInit();
			$this->head->addControl($this->createControl(MetaControl::class)
				->setAttributeList([
					'name' => 'viewport',
					'content' => 'width=device-width, initial-scale=1',
				]));
		}
	}

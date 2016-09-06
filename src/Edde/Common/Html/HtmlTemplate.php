<?php
	declare(strict_types = 1);

	namespace Edde\Common\Html;

	use Edde\Api\Container\IContainer;
	use Edde\Api\Control\IControl;
	use Edde\Api\Html\IHtmlTemplate;
	use Edde\Api\Template\ITemplateManager;
	use Edde\Api\Web\IJavaScriptCompiler;
	use Edde\Api\Web\IStyleSheetCompiler;
	use Edde\Common\AbstractObject;
	use Edde\Common\Container\LazyInjectTrait;

	abstract class HtmlTemplate extends AbstractObject implements IHtmlTemplate {
		use LazyInjectTrait;
		/**
		 * @var IContainer
		 */
		protected $container;
		/**
		 * @var IStyleSheetCompiler
		 */
		protected $styleSheetCompiler;
		/**
		 * @var IJavaScriptCompiler
		 */
		protected $javaScriptCompiler;
		/**
		 * @var ITemplateManager
		 */
		protected $templateManager;
		/**
		 * @var IControl
		 */
		protected $root;
		/**
		 * @var \ReflectionClass
		 */
		protected $reflectionClass;
		/**
		 * @var callable[]
		 */
		protected $controlList;

		public function lazytContainer(IContainer $container) {
			$this->container = $container;
		}

		public function lazytStyleSheetCompiler(IStyleSheetCompiler $styleSheetCompiler) {
			$this->styleSheetCompiler = $styleSheetCompiler;
		}

		public function lazytJavaScriptCompiler(IJavaScriptCompiler $javaScriptCompiler) {
			$this->javaScriptCompiler = $javaScriptCompiler;
		}

		public function lazytTemplateManager(ITemplateManager $templateManager) {
			$this->templateManager = $templateManager;
		}

		public function __call($function, array $parameterList) {
			return call_user_func_array([
				$this->root,
				$function,
			], $parameterList);
		}

		public function include (string $file) {
			$template = $this->templateManager->template($file);
			$template = $template->getInstance($this->container);
			/** @var $template IHtmlTemplate */
			$this->controlList = array_merge($this->getControlList(), $template->getControlList());
			return $this;
		}

		public function getControlList(): array {
			if ($this->controlList === null) {
				$this->controlList = $this->onTemplate();
			}
			return $this->controlList;
		}

		abstract protected function onTemplate();

		public function template(IControl $root) {
			$this->root = $root;
			$this->reflectionClass = new \ReflectionClass($root);
			$this->build();
		}

		public function build() {
			foreach ($this->getControlList() as $callable) {
				$callable($this->root);
			}
		}
	}

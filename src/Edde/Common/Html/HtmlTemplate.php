<?php
	declare(strict_types = 1);

	namespace Edde\Common\Html;

	use Edde\Api\Container\IContainer;
	use Edde\Api\Control\IControl;
	use Edde\Api\Html\IHtmlTemplate;
	use Edde\Api\Template\ITemplateManager;
	use Edde\Api\Template\TemplateException;
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
		protected $controlList = [];

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

		public function include (string $file, IControl $root) {
			$template = $this->templateManager->template($file);
			$template = $template->getInstance($this->container);
			/** @var $template IHtmlTemplate */
			$this->controlList = array_merge($this->getControlList($root), $template->getControlList($root));
			return $this;
		}

		public function getControlList(IControl $root): array {
			$this->root = $root;
			$this->onTemplate();
			return $this->controlList;
		}

		abstract protected function onTemplate();

		public function template(IControl $root) {
			$this->root = $root;
			$this->reflectionClass = new \ReflectionClass($root);
			$this->build();
		}

		public function build() {
			$controlList = $this->getControlList($this->root);
			$controlList[null]($this->root);
		}

		public function control(string $name, IControl $root): IHtmlTemplate {
			if (isset($this->controlList[$name]) === false) {
				throw new TemplateException(sprintf('Requested unknown control block [%s] on [%s].', $name, $root->getNode()
					->getPath()));
			}
			$this->controlList[$name]($root);
			return $this;
		}

		protected function addControl($id, callable $callback) {
			if (isset($this->controlList[$id])) {
				throw new TemplateException(sprintf('An control id [%s] is already taken (there can be automagicall clash problem, ...).', $id));
			}
			$this->controlList[$id] = $callback;
		}
	}

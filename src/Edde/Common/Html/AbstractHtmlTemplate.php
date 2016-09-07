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
	use Edde\Common\Container\LazyInjectTrait;
	use Edde\Common\Template\AbstractTemplate;

	abstract class AbstractHtmlTemplate extends AbstractTemplate implements IHtmlTemplate {
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
		protected $stash;
		/**
		 * @var IControl
		 */
		protected $current;
		/**
		 * @var callable[]
		 */
		protected $controlList;
		protected $importList = [];

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

		public function import(string $file): IHtmlTemplate {
			$this->importList[$file] = $file;
			return $this;
		}

		public function template(IControl $root, array $importList = []): IHtmlTemplate {
			$this->root = $root;
			$this->importList = array_merge($this->importList, $importList);
			$this->getControlList()[null]();
			return $this;
		}

		public function getControlList(): array {
			if ($this->controlList === null) {
				$this->controlList = [];
				$this->stash = [];
				$this->reflectionClass = new \ReflectionClass($this->root);
				$this->onTemplate();
				$callback = $this->getControlList()[null];
				foreach ($this->importList as $import) {
					/** @var $template IHtmlTemplate */
					if ((($template = $this->templateManager->template($import)) instanceof IHtmlTemplate) === false) {
						throw new TemplateException(sprintf('Unsupported included template [%s] type [%s]; template must be instance of [%s].', $import, get_class($template), IHtmlTemplate::class));
					}
					$template->root = $this->root;
					$this->controlList = array_merge($this->controlList, $template->getControlList());
				}
				$this->controlList[null] = $callback;
			}
			return $this->controlList;
		}

		abstract protected function onTemplate();

		public function control(string $name, IControl $root): IControl {
			$this->getControlList();
			if (isset($this->controlList[$name]) === false) {
				throw new TemplateException(sprintf('Requested unknown control block [%s] on [%s].', $name, $root->getNode()
					->getPath()));
			}
			return $this->controlList[$name]($root);
		}

		public function addControl($id, callable $callback, bool $force = false): IHtmlTemplate {
			if (isset($this->controlList[$id]) && $force === false) {
				throw new TemplateException(sprintf('An control id [%s] is already taken (there can be automagicall clash problem, ...).', $id));
			}
			$this->controlList[$id] = $callback;
			return $this;
		}
	}
